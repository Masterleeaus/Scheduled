<?php
namespace App\Support\Install;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The vendor SQL dump is the legacy schema snapshot. All newer Titan/booking
 * tables must be installed using their own source migrations, never by replaying
 * the hundreds of legacy migrations already represented in that snapshot.
 */
final class InitialFeatureMigrations
{
    /** Explicit fresh-install migration set. Unexpected/missing files fail closed. */
    private const REQUIRED_MIGRATIONS = [
        '2026_11_09_000001_create_booking_integration_tables',
        '2026_11_09_000031_create_booking_public_domains_table',
        '2026_11_10_000001_create_titan_landing_campaigns',
        '2026_11_10_000002_create_titan_landing_leads',
        '2026_11_10_000003_create_titan_site_templates',
        '2026_11_10_000005_create_titan_site_leads',
        '2026_11_10_000006_add_attribution_to_titan_site_leads',
        '2026_11_10_000072_create_builder_revisions',
        '2026_11_11_000006_create_titan_brand_proposals_table',
        '2026_11_11_080001_add_design_to_titan_landing_campaigns',
        '2026_11_11_120001_create_booking_payment_events',
        '2026_11_11_140000_add_booking_requests_to_titan_site_leads',
        '2026_11_11_140001_create_titan_business_tenants',
        '2026_11_11_140002_create_titan_site_domains',
        '2026_11_11_150001_create_titan_agent_audit_events',
        '2026_11_11_190000_create_titan_site_booking_settings',
        '2026_11_11_190001_add_published_snapshots_to_titan_landing_campaigns',
        '2026_11_11_270001_create_titan_site_booking_service_maps',
        '2026_11_15_000001_create_central_processed_events',
        '2026_11_15_000002_create_booking_event_outbox_tables',
        '2026_11_15_000003_create_central_user_links',
    ];

    /** Tables created by the required migrations; verify every one after import. */
    private const REQUIRED_TABLES = [
        'booking_customer_links',
        'booking_payment_events',
        'booking_public_domains',
        'booking_request_keys',
        'booking_service_links',
        'booking_tenant_instances',
        'builder_revisions',
        'central_user_links',
        'titan_agent_audit_events',
        'titan_brand_proposals',
        'titan_business_memberships',
        'titan_business_tenants',
        'titan_landing_campaigns',
        'titan_landing_leads',
        'titan_site_booking_service_maps',
        'titan_site_booking_settings',
        'titan_site_domains',
        'titan_site_leads',
        'titan_site_revisions',
        'titan_sites',
    ];

    /** Critical columns from later ALTER migrations. */
    private const REQUIRED_COLUMNS = [
        'titan_site_leads' => ['preferred_date', 'preferred_time', 'request_kind', 'booking_details', 'utm_source'],
        'titan_landing_campaigns' => ['design_json', 'published_slug', 'published_snapshot'],
    ];

    /** @return array<string,string> basename => absolute path */
    public static function files(string $migrationDirectory): array
    {
        if (!is_dir($migrationDirectory)) {
            throw new RuntimeException('Application migrations directory is missing.');
        }
        $files = glob(rtrim($migrationDirectory, '/').'/2026_*.php');
        if ($files === false || count($files) !== count(self::REQUIRED_MIGRATIONS)) {
            throw new RuntimeException('Fresh-install feature migrations are missing.');
        }
        $result = [];
        foreach ($files as $path) {
            $basename = basename($path, '.php');
            if (!preg_match('/^2026_\d{2}_\d{2}_\d{6}_[a-z0-9_]+$/D', $basename)
                || !is_file($path) || !is_readable($path)) {
                throw new RuntimeException('Invalid feature migration file.');
            }
            $result[$basename] = realpath($path);
        }
        ksort($result, SORT_STRING);
        if (array_keys($result) !== self::REQUIRED_MIGRATIONS) {
            throw new RuntimeException('Fresh-install migration set differs from the verified release.');
        }
        return $result;
    }

    public static function apply(string $migrationDirectory): void
    {
        $files = self::files($migrationDirectory);
        // Laravel 9 migrate supports --path=* with absolute paths and --realpath.
        // It tracks completion in the existing migrations table imported from SQL.
        $exit = Artisan::call('migrate', [
            '--path' => array_values($files), '--realpath' => true, '--force' => true,
        ]);
        if ($exit !== 0) {
            throw new RuntimeException('Feature migrations failed; installation cannot continue.');
        }
        self::verify($migrationDirectory);
    }

    public static function verify(string $migrationDirectory): void
    {
        $files = self::files($migrationDirectory);
        $installed = DB::table('migrations')
            ->whereIn('migration', array_keys($files))
            ->distinct()->pluck('migration')->all();
        if (count(array_intersect(array_keys($files), $installed)) !== count($files)) {
            throw new RuntimeException('Feature migrations are incomplete.');
        }
        // A migration ledger entry alone does not prove the required schema exists.
        foreach (self::REQUIRED_TABLES as $table) {
            if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                throw new RuntimeException('Required feature table is missing: '.$table);
            }
        }
        foreach (self::REQUIRED_COLUMNS as $table => $columns) {
            foreach ($columns as $column) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn($table, $column)) {
                    throw new RuntimeException('Required feature column is missing: '.$table.'.'.$column);
                }
            }
        }
    }
}
