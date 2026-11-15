<?php
namespace App\Console\Commands;

use App\Booking\Diagnostics\BookingTenantProbe;
use Illuminate\Console\Command;

/** Operator-only read-only verification. Never automatically enables public booking commits. */
class VerifyCleantoTenants extends Command
{
    protected $signature = 'booking:verify-tenants {--owner= : Inspect only one configured owner ID} {--json : Machine-readable report}';
    protected $description = 'Inspect real Cleanto tenant database schema and trusted connections without booking writes';

    public function handle(): int
    {
        if (!extension_loaded('pdo_mysql')) {
            $this->error('pdo_mysql is unavailable; no live MariaDB evidence can be collected.');
            return 2;
        }
        $profiles = config('booking_internal.public_commit_tenants', []);
        if (!is_array($profiles) || !$profiles) {
            $this->error('No trusted booking tenant profiles configured.');
            return 2;
        }
        $raw = $this->option('owner');
        if ($raw !== null && (!ctype_digit((string)$raw) || (int)$raw < 1)) {
            $this->error('Owner must be a positive integer.');
            return 2;
        }
        $owners = $raw !== null ? [(int)$raw] : array_keys($profiles);
        require_once dirname(base_path()).'/private/booking-sidecar/BookingSchemaInspector.php';
        $required = \Titan\BookingSidecar\BookingSchemaInspector::expected();
        $report = ['generated_at' => gmdate('c'), 'read_only' => true, 'production_certified' => false, 'tenants' => []];
        foreach ($owners as $owner) {
            $report['tenants'][] = BookingTenantProbe::inspect((int)$owner, $profiles, $required);
        }
        $passed = count(array_filter($report['tenants'], static fn($r) => $r['status'] === 'read_only_checks_passed'));
        $report['read_only_passed'] = $passed;
        $report['total'] = count($report['tenants']);
        if ($this->option('json')) $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        else {
            foreach ($report['tenants'] as $r) $this->line('Owner '.$r['owner'].': '.$r['status']);
            $this->warn('Read-only database checks cannot certify concurrent booking, pricing, payments or production readiness.');
        }
        return $passed === $report['total'] ? 0 : 1;
    }
}
