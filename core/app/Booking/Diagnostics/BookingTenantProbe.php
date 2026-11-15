<?php
declare(strict_types=1);
namespace App\Booking\Diagnostics;

use App\Booking\Public\Checkout\CheckoutDeploymentProfile;
use Illuminate\Support\Facades\DB;

/** Read-only tenant inspection; never asserts that concurrency or settlement tests passed. */
final class BookingTenantProbe
{
    public static function inspect(int $owner, array $profiles, array $required): array
    {
        $result = ['owner' => $owner, 'status' => 'blocked', 'checks' => [], 'errors' => []];
        try {
            $profile = CheckoutDeploymentProfile::tenant($owner, $profiles);
            $result['checks']['trusted_profile'] = true;
            $pdo = DB::connection($profile['connection'])->getPdo();
            if (!$pdo instanceof \PDO) throw new \RuntimeException('Tenant connection is not PDO');
            if ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) !== 'mysql') throw new \RuntimeException('Tenant connection is not MySQL/MariaDB');
            if ($pdo->getAttribute(\PDO::ATTR_EMULATE_PREPARES) === true) throw new \RuntimeException('Emulated SQL prepares are forbidden');
            $snapshot = BookingSchemaSnapshot::inspect($pdo, $required);
            $result['checks']['native_prepares'] = true;
            $result['schema'] = $snapshot;
            $expected = $profile['tenant']['database'] ?? null;
            $result['checks']['tenant_database_match'] = is_string($expected) && hash_equals($expected, $snapshot['database']);
            if (!$result['checks']['tenant_database_match']) $result['errors'][] = 'Connected database is not the configured tenant database';
            if (!$snapshot['schema_ok']) $result['errors'][] = 'Required Cleanto table columns missing';
            $result['status'] = empty($result['errors']) ? 'read_only_checks_passed' : 'blocked';
        } catch (\Throwable $error) {
            // Never expose DB credentials, PDO DSNs, raw SQL failures, or connection settings.
            $result['errors'][] = 'Tenant configuration, database connection, or schema inspection failed';
            $result['diagnostic_type'] = get_class($error);
        }
        $result['checks']['live_concurrency_verified'] = false;
        $result['checks']['payment_settlement_verified'] = false;
        $result['production_certified'] = false;
        return $result;
    }
}
