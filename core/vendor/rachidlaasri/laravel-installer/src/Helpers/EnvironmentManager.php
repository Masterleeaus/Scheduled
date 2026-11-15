<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (! file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param Request $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = trans('installer_messages.environment.success');

        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        } catch (Exception $e) {
            $message = trans('installer_messages.environment.errors');
        }

        return $message;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function saveFileWizard(Request $request)
    {
        $results = trans('installer_messages.environment.success');

        $existing = $this->getEnvContent();
        if (!preg_match('/^APP_KEY=(base64:[A-Za-z0-9+\/=]+)\s*$/m', $existing, $matches)
            || strlen((string) base64_decode(substr($matches[1], 7), true)) !== 32) {
            throw new \RuntimeException('Missing valid pre-initialized installation key.');
        }
        $appKey = $matches[1];
        // The current request's encryption key must remain identical to the written key.
        if ((string) config('app.key') !== $appKey) {
            throw new \RuntimeException('Application key is out of sync; clear cached config and restart installation.');
        }

        // Preserve all existing application/integration configuration. Installer must
        // never replace a populated .env with a short legacy whitelist.
        try {
            \App\Support\Install\EnvironmentPatchWriter::apply($this->envPath, [
                'APP_NAME' => (string) $request->input('app_name'),
                'APP_ENV' => 'production',
                'APP_KEY' => $appKey,
                'APP_DEBUG' => 'false',
                'APP_URL' => (string) $request->input('app_url'),
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => (string) $request->input('database_hostname'),
                'DB_PORT' => (string) \App\Support\Install\DatabaseEndpoint::port($request->input('database_port')),
                'DB_DATABASE' => (string) $request->input('database_name'),
                'DB_USERNAME' => (string) $request->input('database_username'),
                'DB_PASSWORD' => (string) $request->input('database_password', ''),
                'CACHE_DRIVER' => 'file',
                'SESSION_DRIVER' => 'file',
            ]);
        } catch (\Throwable $e) {
            // No passwords, tokens or raw .env content in installer errors.
            return trans('installer_messages.environment.errors');
        }
        return $results;
    }
}
