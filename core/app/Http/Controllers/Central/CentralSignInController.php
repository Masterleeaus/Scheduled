<?php

namespace App\Http\Controllers\Central;

use App\Central\CentralException;
use App\Central\Config\CentralConfig;
use App\Central\Http\CurlHttpTransport;
use App\Central\Link\CentralUserLinkResolver;
use App\Central\Link\DatabaseCentralUserLinkResolver;
use App\Central\Oidc\CentralSignIn;
use App\Central\Oidc\OidcDiscovery;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Central sign-in for the web user guard. login and callback return 404 unless CENTRAL_SIGNIN_ENABLED is on.
 * logout always ends the local session, whatever the flag says, so a Central session can never be stuck.
 * Only an existing local user reached through an approved non-revoked database link can sign in. This controller never creates
 * users and never touches the admin guard. Local, Google and Facebook login are separate and unchanged.
 */
class CentralSignInController extends Controller
{
    private const FAILURE_MESSAGE = 'Sign-in with Central failed. Please try again.';

    public function login(Request $request)
    {
        [, $signIn] = $this->signIn();
        try {
            $authorization = $signIn->begin();
        } catch (CentralException $e) {
            // Discovery or transport failure: send the user back to login with a generic message, never a 500.
            return $this->failed($request);
        }
        $request->session()->put(CentralSignIn::SESSION_KEY, [
            'state' => $authorization->state,
            'nonce' => $authorization->nonce,
            'code_verifier' => $authorization->codeVerifier,
        ]);

        return redirect()->away($authorization->url);
    }

    public function callback(Request $request)
    {
        [, $signIn, $links] = $this->signIn(true);
        $pending = $request->session()->pull(CentralSignIn::SESSION_KEY, []);

        if ($request->filled('error')) {
            return $this->failed($request);
        }

        try {
            $verified = $signIn->complete(
                is_array($pending) ? $pending : [],
                $this->stringInput($request->query('state')),
                $this->stringInput($request->query('code')),
            );
        } catch (CentralException $e) {
            return $this->failed($request);
        }

        try {
            $localUserId = $links->localUserId($verified['iss'], $verified['sub']);
            $user = $localUserId === null ? null : User::find($localUserId);
        } catch (\Throwable $e) {
            // Missing migrations or unavailable database must never bypass verified link lookup.
            return $this->failed($request);
        }
        if ($user === null || (string) $user->status === '0') {
            return $this->failed($request);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('user-dashboard');
    }

    public function logout(Request $request)
    {
        // Local logout only. It does not read the Central flag, so it works whether sign-in is on or off.
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }

    private function stringInput(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    private function failed(Request $request)
    {
        $request->session()->forget(CentralSignIn::SESSION_KEY);

        return redirect()->route('user.login')->with('error', self::FAILURE_MESSAGE);
    }

    /**
     * Builds the sign-in service from the environment. Aborts with 404 while the flag is off, so the routes
     * look absent. Only a 404 is returned in that case, with no configuration detail.
     *
     * @return array{0:CentralConfig, 1:CentralSignIn, 2?:CentralUserLinkResolver}  The third item only with $withLinks.
     */
    private function signIn(bool $withLinks = false): array
    {
        $config = new CentralConfig();
        if (!$config->signInEnabled()) {
            abort(404);
        }
        $http = new CurlHttpTransport(10, $config->allowInsecureLocalhost());
        $discovery = new OidcDiscovery(
            $http,
            $config->oidcDiscoveryUrl(),
            $config->centralOrigin(),
            $config->allowInsecureLocalhost(),
        );
        $signIn = new CentralSignIn(
            $http,
            $discovery,
            $config->oidcClientId(),
            $config->oidcClientSecret(),
            $config->oidcRedirectUri(),
            $config->oidcAudience(),
        );

        return $withLinks ? [$config, $signIn, new DatabaseCentralUserLinkResolver()] : [$config, $signIn];
    }
}
