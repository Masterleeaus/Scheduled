<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckStatus
{
    public function handle($request, Closure $next)
    {
        // A NULL role_id represents the built-in administrator; it must not
        // bypass the inactive-account check after bootstrap or later suspension.
        $admin = Auth::guard('admin')->user();
        if ($admin && (int) $admin->status !== 1) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Session::flash('warning', 'Your administrator account is inactive.');
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
