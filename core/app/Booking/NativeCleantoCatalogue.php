<?php
declare(strict_types=1);
namespace App\Booking;

use App\Booking\Widget\CatalogSnapshot;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Read Cleanto's original tables through a server-configured Laravel MySQL connection.
 * No direct public route, tenant supplied DSN, schema mutation or booking write.
 */
final class NativeCleantoCatalogue
{
    public function read(array $tenant, array $binding): array
    {
        if (($binding['enabled'] ?? false) !== true) {
            throw new RuntimeException('Native catalogue is disabled for this owner.');
        }
        $connection = $binding['connection'] ?? null;
        $expected = $binding['database'] ?? null;
        if (!is_string($connection) || !preg_match('/^[A-Za-z][A-Za-z0-9_]{0,63}$/D', $connection)
            || !is_string($expected) || !preg_match('/^[A-Za-z0-9_]{1,64}$/D', $expected)
            || !hash_equals((string) ($tenant['database_reference'] ?? ''), $expected)) {
            throw new RuntimeException('Tenant connection is not independently configured.');
        }
        $configuration = config('database.connections.'.$connection);
        if (!is_array($configuration) || ($configuration['driver'] ?? '') !== 'mysql'
            || ($configuration['database'] ?? null) !== $expected) {
            throw new RuntimeException('Tenant MySQL connection does not match registry.');
        }
        $db = DB::connection($connection);
        $identity = $db->selectOne('SELECT DATABASE() AS tenant_database');
        if (!is_object($identity) || !is_string($identity->tenant_database ?? null)
            || !hash_equals($expected, $identity->tenant_database)) {
            throw new RuntimeException('Connected Cleanto database identity mismatch.');
        }
        $services = $this->rows($db, "SELECT id, title FROM ct_services WHERE status='E' ORDER BY position,id LIMIT 251", 250);
        $methods = $this->rows($db, "SELECT m.id,m.service_id,m.method_title AS title FROM ct_services_method m INNER JOIN ct_services s ON s.id=m.service_id AND s.status='E' WHERE m.status='E' ORDER BY m.position,m.id LIMIT 1001", 1000);
        $units = $this->rows($db, "SELECT u.id,u.methods_id AS method_id,u.services_id AS service_id,u.units_title AS title FROM ct_service_methods_units u INNER JOIN ct_services_method m ON m.id=u.methods_id AND m.service_id=u.services_id AND m.status='E' INNER JOIN ct_services s ON s.id=u.services_id AND s.status='E' WHERE u.status='E' ORDER BY u.position,u.id LIMIT 3001", 3000);
        $staff = $this->rows($db, "SELECT id,first_name,last_name FROM ct_users WHERE status='E' AND usertype='staff' ORDER BY first_name,last_name,id LIMIT 251", 250);
        return CatalogSnapshot::build([
            'services' => $this->normaliseTitles($services),
            'methods' => $this->normaliseTitles($methods),
            'units' => $this->normaliseTitles($units),
            'staff' => array_map(static fn(array $row): array => [
                'id' => $row['id'],
                'name' => trim(strip_tags((string) $row['first_name'].' '.(string) $row['last_name'])),
            ], $staff),
        ]);
    }

    private function rows(object $db, string $sql, int $maximum): array
    {
        $rows = $db->select($sql);
        if (!is_array($rows) || count($rows) > $maximum) {
            throw new RuntimeException('Native Cleanto catalogue exceeds configured limits.');
        }
        return array_map(static fn($row): array => (array) $row, $rows);
    }

    private function normaliseTitles(array $rows): array
    {
        return array_map(static function (array $row): array {
            $row['title'] = trim(strip_tags((string) ($row['title'] ?? '')));
            return $row;
        }, $rows);
    }
}
