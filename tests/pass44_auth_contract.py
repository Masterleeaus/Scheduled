from pathlib import Path
base=Path(__file__).resolve().parents[1]
login=(base/'core/app/Http/Controllers/Admin/LoginController.php').read_text()
status=(base/'core/app/Http/Middleware/CheckStatus.php').read_text()
checks={
 'login rejects disabled users': "'status' => 1" in login and "Auth::guard('admin')->attempt" in login,
 'login regenerates session': '$request->session()->regenerate()' in login,
 'logout invalidates session': '$request->session()->invalidate()' in login,
 'logout rotates CSRF': '$request->session()->regenerateToken()' in login,
 'no role bypass': '!empty(' not in status and '(int) $admin->status !== 1' in status,
 'inactive account logged out': "Auth::guard('admin')->logout()" in status,
 'inactive session invalidated': '$request->session()->invalidate()' in status,
 'inactive CSRF regenerated': '$request->session()->regenerateToken()' in status,
 'guest allowed through middleware': 'return $next($request);' in status,
}
for k, v in checks.items():print(('PASS' if v else 'FAIL'),k)
if not all(checks.values()):raise SystemExit(1)
print('PASS:',len(checks),'source contract assertions')
