<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use RachidLaasri\LaravelInstaller\Events\EnvironmentSaved;
use RachidLaasri\LaravelInstaller\Helpers\EnvironmentManager;
use Illuminate\Support\Facades\Session;
use Validator;

class EnvironmentController extends Controller
{
    /**
     * @var EnvironmentManager
     */
    protected $EnvironmentManager;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
    }

    /**
     * Display the Environment menu page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentMenu()
    {
        return view('vendor.installer.environment');
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentWizard()
    {
        if (session('installer_license_verified') !== true) {
            Session::flash('license_error', 'Please, verify your license first!');
            return redirect()->route('LaravelInstaller::license');
        }
        $envConfig = $this->EnvironmentManager->getEnvContent();

        return view('vendor.installer.environment-wizard', compact('envConfig'));
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentClassic()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();

        return view('vendor.installer.environment-classic', compact('envConfig'));
    }

    /**
     * Processes the newly saved environment configuration (Classic).
     *
     * @param Request $input
     * @param Redirector $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveClassic(Request $input, Redirector $redirect)
    {
        abort(404); // Unvalidated raw .env writes are not an installation method.
        $message = $this->EnvironmentManager->saveFileClassic($input);

        event(new EnvironmentSaved($input));

        return $redirect->route('LaravelInstaller::environmentClassic')
                        ->with(['message' => $message]);
    }

    /**
     * Processes the newly saved environment configuration (Form Wizard).
     *
     * @param Request $request
     * @param Redirector $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveWizard(Request $request, Redirector $redirect)
    {
        if (session('installer_license_verified') !== true) {
            Session::flash('license_error', 'Please verify your purchase before installation.');
            return redirect()->route('LaravelInstaller::license');
        }
        $validator = Validator::make($request->all(), config('installer.environment.form.rules'));
        if ($validator->fails()) {
            return $redirect->route('LaravelInstaller::environmentWizard')->withErrors($validator)->withInput($request->except('database_password'));
        }
        // The SQL importer needs both drivers; fail before touching database state.
        if (!extension_loaded('pdo_mysql') || !extension_loaded('mysqli')) {
            return $redirect->route('LaravelInstaller::requirements')->withErrors([
                'database_connection' => 'Installation requires both PHP pdo_mysql and mysqli extensions.'
            ]);
        }
        // One validated port is used for Laravel PDO, mysqli import and the saved .env.
        $databasePort = \App\Support\Install\DatabaseEndpoint::port($request->input('database_port'));
        if (! $this->checkDatabaseConnection($request)) {
            return $redirect->route('LaravelInstaller::environmentWizard')->withErrors(['database_connection' => 'Cannot connect to the selected database. Check your credentials and PHP pdo_mysql extension.'])->withInput($request->except('database_password'));
        }
        try {
            // Fail BEFORE SQL import if the env cannot be safely written.
            if (!is_file(base_path('.env')) || !is_writable(base_path('.env'))) {
                throw new \RuntimeException('Installation environment is not writable.');
            }
            // Validate the bundled schema and the target database before changing
            // either the environment or database. MySQL DDL cannot be rolled back.
            \App\Support\Install\LegacySqlImporter::preflight(base_path('../installer/database.sql'));
            $connection = new \mysqli(
                $request->input('database_hostname'),
                $request->input('database_username'),
                $request->input('database_password'),
                $request->input('database_name'),
                $databasePort
            );
            $schemaLockHeld = false;
            try {
                // Serialize fresh installs against this selected MariaDB database.
                // The lock must cover BOTH legacy SQL and the feature migrations.
                // A second request must never pass the empty-schema check while the
                // first request is still installing tables.
                \App\Support\Install\InstallationDatabaseLock::acquire($connection);
                $schemaLockHeld = true;
                // PDO executes Laravel feature migrations; mysqli imports legacy SQL.
                // Verify they point to the SAME database server before any DDL.
                \App\Support\Install\DatabaseConnectionIdentity::assertMatching($connection, DB::connection());
                if (! $connection->set_charset('utf8mb4')) {
                    throw new \RuntimeException('Database does not support required utf8mb4 charset.');
                }
                \App\Support\Install\LegacySqlImporter::ensureEmptyDatabase($connection);

                // A failed environment write must NEVER follow a schema import.
                // Persist configuration before executing any nontransactional SQL.
                $result = $this->EnvironmentManager->saveFileWizard($request);
                if (! is_string($result) || $result !== trans('installer_messages.environment.success')) {
                    throw new \RuntimeException('Unable to save environment settings; check core/.env write permissions.');
                }

                // The same connection remains open: import rechecks emptiness and
                // verifies the resulting account tables before we advance setup.
                \App\Support\Install\LegacySqlImporter::import($connection, base_path('../installer/database.sql'));
                // Legacy SQL does not include the new booking, site or tenant tables.
                // Install only 2026 feature migrations, not the already-imported legacy set.
                \App\Support\Install\InitialFeatureMigrations::apply(database_path('migrations'));
            } finally {
                try {
                    if ($schemaLockHeld) {
                        \App\Support\Install\InstallationDatabaseLock::release($connection);
                    }
                } finally {
                    $connection->close();
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Installer schema stage failed', ['reason' => $e->getMessage()]);
            // Do not echo exception details or database credentials in a public installer.
            return $redirect->route('LaravelInstaller::environmentWizard')->withErrors([
                'database_connection' => 'Installation failed. Check server logs. For a failed schema import, create a new empty database before retrying.'
            ]);
        }

        // The importer strips demo login accounts; a fresh admin MUST be created.
        $request->session()->regenerate();
        $request->session()->put('installer_schema_imported', true);
        event(new EnvironmentSaved($request));
        return redirect()->route('LaravelInstaller::administrator');
    }

    /**
     * TODO: We can remove this code if PR will be merged: https://github.com/RachidLaasri/LaravelInstaller/pull/162
     * Validate database connection with user credentials (Form Wizard).
     *
     * @param Request $request
     * @return bool
     */
    private function checkDatabaseConnection(Request $request)
    {
        $connection = 'mysql';

        $settings = config("database.connections.$connection");
        // The installer collects an explicit endpoint. Do not inherit a stale
        // DATABASE_URL, unix socket or replica connection from an existing .env.
        // Those settings can redirect PDO while mysqli imports into a different DB.
        unset($settings['read'], $settings['write']);
        $settings['url'] = null;
        $settings['unix_socket'] = '';

        config([
            'database' => [
                'default' => $connection,
                'connections' => [
                    $connection => array_merge($settings, [
                        'driver' => $connection,
                        'host' => $request->input('database_hostname'),
                        'port' => \App\Support\Install\DatabaseEndpoint::port($request->input('database_port')),
                        'database' => $request->input('database_name'),
                        'username' => $request->input('database_username'),
                        'password' => $request->input('database_password'),
                    ]),
                ],
            ],
        ]);

        DB::purge();

        try {
            DB::connection()->getPdo();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
