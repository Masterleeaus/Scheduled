<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Redirect public HTTP traffic into the installer without killing Laravel's
 * console bootstrap, background workers or migration commands.
 */
final class RedirectUninstalledToInstaller
{
    public function handle(Request $request, Closure $next)
    {
        if (! is_file(storage_path('installed'))
            && ! $request->is('install')
            && ! $request->is('install/*')) {
            return redirect('/install');
        }

        return $next($request);
    }
}
