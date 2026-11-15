<?php
namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Support\Install\InstallerLicenseVerifier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

/** The purchase key and API token must never be included in HTML, logs or archive configuration. */
class LicenseController extends Controller
{
    public function license()
    {
        return view('vendor.installer.license');
    }

    public function licenseCheck(Request $request)
    {
        abort_if(is_file(storage_path('installed')), 404);
        $details = $request->validate([
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:100',
            'purchase_code' => 'required|string|max:150',
        ]);
        // An API token supplied by the server operator; never embed vendor credentials in source.
        $token = (string) config('installer.license.envato_api_token', '');
        if ($token === '') {
            return back()->with('license_error', 'Purchase verification is not configured. Ask the server administrator to set INSTALLER_ENVATO_TOKEN.');
        }
        try {
            $response = Http::acceptJson()->withToken($token)->timeout(12)->get(
                'https://api.envato.com/v3/market/author/sale',
                ['code' => $details['purchase_code']]
            );
            if (!$response->successful() || !InstallerLicenseVerifier::matches(
                $response->json(), $details['username'], 24646161
            )) {
                return back()->with('license_error', 'Purchase verification was unsuccessful. Check the username and purchase code.');
            }
        } catch (\Throwable $e) {
            // Never expose remote response bodies, bearer tokens, credentials or exception text.
            return back()->with('license_error', 'Purchase verification service is unavailable. Please try again.');
        }
        $request->session()->regenerate();
        $request->session()->put('installer_license_verified', true);
        return redirect()->route('LaravelInstaller::environmentWizard')
            ->with('license_success', 'Purchase verified successfully.');
    }
}
