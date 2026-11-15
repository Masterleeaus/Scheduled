<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Support\Install\InstallationCompletionGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RachidLaasri\LaravelInstaller\Events\LaravelInstallerFinished;
use RachidLaasri\LaravelInstaller\Helpers\FinalInstallManager;
use RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager;

class FinalController extends Controller
{
    /** Display the last confirmation without making any persistent changes. */
    public function review(Request $request)
    {
        $this->requireVerifiedSetup($request);
        return view('vendor.installer.final-review');
    }

    private function requireVerifiedSetup(Request $request): void
    {
        abort_if(is_file(storage_path('installed')), 404);
        abort_unless($request->session()->get('installer_schema_imported') === true
            && $request->session()->get('installer_admin_created') === true, 404);
    }

    /** Only a CSRF-protected POST may commit the installed marker. */
    public function finish(Request $request, InstalledFileManager $fileManager, FinalInstallManager $finalInstall)
    {
        // Check provenance and installation marker before reading the selected database.
        $this->requireVerifiedSetup($request);

        // Schema::hasTable is read-only and fails closed on a broken connection.
        $schemaValid = Schema::hasTable('admins') && Schema::hasTable('users') && Schema::hasTable('roles');
        abort_unless($schemaValid, 409, 'Installation account tables are not available.');
        try {
            \App\Support\Install\InitialFeatureMigrations::verify(database_path('migrations'));
        } catch (\Throwable $e) {
            abort(409, 'Application feature tables are incomplete.');
        }

        $admins = DB::table('admins');
        $admin = $admins->first(['id', 'status', 'password']);
        $state = [
            'schema_imported' => $request->session()->get('installer_schema_imported') === true,
            'administrator_created' => $request->session()->get('installer_admin_created') === true,
            'identity_schema_valid' => $schemaValid,
            'administrator_count' => DB::table('admins')->count(),
            'active_administrator_count' => DB::table('admins')->where('status', 1)->count(),
            'customer_count' => DB::table('users')->count(),
            'administrator_hash_present' => $admin && is_string($admin->password) && strlen($admin->password) >= 50,
            'app_key' => (string) config('app.key'),
            'environment_readable' => is_file(base_path('.env')) && is_readable(base_path('.env')),
            'storage_writable' => is_dir(storage_path()) && is_writable(storage_path()),
            'installed_marker_exists' => file_exists(storage_path('installed')),
        ];
        $problems = InstallationCompletionGuard::problems($state);
        abort_unless($problems === [], 409, 'Installation prerequisites are incomplete.');

        // A thrown exception keeps the one-time session tokens intact for retry.
        // Never expose database secrets or internal configuration on the finish page.
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();
        abort_unless(is_file(storage_path('installed')), 500);
        $request->session()->forget(['installer_schema_imported', 'installer_admin_created']);
        event(new LaravelInstallerFinished);

        return view('vendor.installer.finished', compact('finalMessages', 'finalStatusMessage'));
    }
}
