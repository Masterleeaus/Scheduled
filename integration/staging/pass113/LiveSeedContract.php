<?php
declare(strict_types=1);
namespace Titan\Staging\Pass113;

/** Pure validation of read-only MariaDB metadata and authentic synthetic catalogue rows. */
final class LiveSeedContract {
    public static function verify(array $tables, array $rows): array {
        $required = ['ct_bookings','ct_order_client_info','ct_services','ct_services_method',
          'ct_service_methods_units','ct_services_methods_units_rate','ct_admin_info',
          'ct_week_days_available','titan_booking_requests','titan_booking_orders'];
        foreach ($required as $table) {
            if (!array_key_exists($table, $tables) || strtoupper((string)$tables[$table]) !== 'INNODB') {
                throw new \RuntimeException('Missing/nontransactional required table: '.$table);
            }
        }
        if (count($tables) !== 38) throw new \RuntimeException('Expected exactly 38 native and ledger tables');
        foreach (['service','method','unit','rate','staff'] as $key) {
            if (($rows[$key] ?? null) !== 1) throw new \RuntimeException('Missing or duplicate enabled synthetic '.$key);
        }
        if (($rows['schedule_days'] ?? null) !== 5 || ($rows['schedule_distinct_days'] ?? null) !== 5 ||
            ($rows['schedule_total_rows'] ?? null) !== 5 || ($rows['schedule_bad_rows'] ?? null) !== 0) {
            throw new \RuntimeException('Expected exactly five distinct, enabled weekday 08:00–18:00 schedule rows');
        }
        if (($rows['linked_methods'] ?? null) !== 1 || ($rows['linked_units'] ?? null) !== 1 || ($rows['linked_rates'] ?? null) !== 1) {
            throw new \RuntimeException('Synthetic enabled service/pricing linkage invalid');
        }
        if (($rows['staff_service'] ?? null) !== '1') throw new \RuntimeException('Synthetic staff-service assignment invalid');
        if (($rows['booking_count'] ?? null) !== 0 || ($rows['ledger_count'] ?? null) !== 0 ||
            ($rows['order_count'] ?? null) !== 0 || ($rows['ledger_order_count'] ?? null) !== 0) {
            throw new \RuntimeException('Fixture database already contains booking or order records');
        }
        return ['status'=>'LIVE_SEED_READBACK_OK','table_count'=>count($tables),'schedule_days'=>5,
          'booking_count'=>0,'order_count'=>0,'acceptance_booking_committed'=>false];
    }
}
