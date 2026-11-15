<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** First administrator setup after the one-time fresh schema import. */
class AdministratorController extends Controller
{
    private function requireImport(Request $request): void
    {
        abort_unless($request->session()->get('installer_schema_imported') === true, 404);
        abort_if(file_exists(storage_path('installed')), 404);
    }

    public function create(Request $request)
    {
        $this->requireImport($request);
        abort_if(DB::table('admins')->exists(), 409, 'Administrator setup has already been completed.');
        return view('vendor.installer.administrator');
    }

    public function store(Request $request)
    {
        $this->requireImport($request);
        $details = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:14|max:128|confirmed',
        ]);

        // Acquire the connection-scoped mutex BEFORE beginning the transaction.
        // Releasing it inside the transaction permits a second request to read
        // an uncommitted admin row and race the first installation.
        $db = DB::connection();
        $lock = $db->selectOne("SELECT GET_LOCK('plusagency_first_admin', 5) AS acquired");
        if (! $lock || (int) $lock->acquired !== 1) {
            throw new \RuntimeException('Administrator setup is busy; retry.');
        }
        try {
            // The named lock stays held until DB::transaction has committed or
            // rolled back. Use the SAME connection for both operations.
            $db->transaction(function () use ($details, $db) {
                if ($db->table('admins')->exists()) {
                    throw new \RuntimeException('An administrator already exists.');
                }
                $db->table('admins')->insert([
                    'role_id' => null, 'username' => 'admin', 'email' => $details['email'],
                    'first_name' => $details['first_name'], 'last_name' => $details['last_name'],
                    'image' => null, 'password' => Hash::make($details['password']),
                    'status' => 1, 'created_at' => now(), 'updated_at' => now(),
                ]);
            });
        } finally {
            // MySQL named locks are not transaction-scoped: always release,
            // including on validation, insert or commit failure.
            $db->selectOne("SELECT RELEASE_LOCK('plusagency_first_admin') AS released");
        }

        // Retain the schema-import proof until finalisation has verified the database.
        // Clearing it here would make the final step reject every legitimate install.
        $request->session()->put('installer_admin_created', true);
        $request->session()->regenerate();
        return redirect()->route('LaravelInstaller::final');
    }
}
