<?php
declare(strict_types=1);
require_once __DIR__.'/LiveSeedContract.php';
use Titan\Staging\Pass113\LiveSeedContract;

/** Actual read-only check executed in the disposable PHP + MariaDB staging network. */
try {
    if (!extension_loaded('pdo_mysql')) throw new RuntimeException('pdo_mysql unavailable');
    $host=getenv('BOOKING_TEST_HOST'); $dbName=getenv('BOOKING_TEST_DATABASE');
    $username=getenv('BOOKING_TEST_USER'); $password=getenv('BOOKING_TEST_PASSWORD');
    if ($host !== 'mariadb-test' || $dbName !== 'titan_booking_test_acceptance' ||
        $username !== 'booking_test' || !is_string($password) || $password === '') {
        throw new RuntimeException('Disposable-only database configuration required');
    }
    $db=new PDO('mysql:host=mariadb-test;dbname=titan_booking_test_acceptance;charset=utf8mb4',
        $username,$password,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_TIMEOUT=>5]);
    $tables=[];
    $statement=$db->query("SELECT TABLE_NAME,ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE'");
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) $tables[$row['TABLE_NAME']]=$row['ENGINE'];
    $queries=[
      'service'=>"SELECT COUNT(*) FROM ct_services WHERE id=1 AND status='E'",
      'method'=>"SELECT COUNT(*) FROM ct_services_method WHERE id=1 AND service_id=1 AND status='E'",
      'unit'=>"SELECT COUNT(*) FROM ct_service_methods_units WHERE id=1 AND services_id=1 AND methods_id=1 AND status='E'",
      'rate'=>"SELECT COUNT(*) FROM ct_services_methods_units_rate WHERE id=1 AND units_id=1 AND rates>0",
      'staff'=>"SELECT COUNT(*) FROM ct_admin_info WHERE id=1 AND enable_booking='Y' AND schedule_type='W'",
      'schedule_days'=>"SELECT COUNT(*) FROM ct_week_days_available WHERE provider_id=1 AND week_id=1 AND weekday_id BETWEEN 1 AND 5 AND off_day='N' AND provider_schedule_type='W' AND day_start_time='08:00:00' AND day_end_time='18:00:00'",
      'schedule_distinct_days'=>"SELECT COUNT(DISTINCT weekday_id) FROM ct_week_days_available WHERE provider_id=1 AND week_id=1 AND weekday_id BETWEEN 1 AND 5 AND off_day='N' AND provider_schedule_type='W' AND day_start_time='08:00:00' AND day_end_time='18:00:00'",
      'schedule_total_rows'=>"SELECT COUNT(*) FROM ct_week_days_available WHERE provider_id=1",
      'schedule_bad_rows'=>"SELECT COUNT(*) FROM ct_week_days_available WHERE provider_id=1 AND (week_id<>1 OR weekday_id NOT BETWEEN 1 AND 5 OR off_day<>'N' OR provider_schedule_type<>'W' OR day_start_time<>'08:00:00' OR day_end_time<>'18:00:00')",
      'linked_methods'=>"SELECT COUNT(*) FROM ct_services_method m JOIN ct_services s ON s.id=m.service_id WHERE s.id=1 AND s.status='E' AND m.id=1 AND m.status='E'",
      'linked_units'=>"SELECT COUNT(*) FROM ct_service_methods_units u JOIN ct_services_method m ON m.id=u.methods_id WHERE u.id=1 AND m.id=1 AND u.services_id=m.service_id AND u.status='E' AND m.status='E'",
      'linked_rates'=>"SELECT COUNT(*) FROM ct_services_methods_units_rate r JOIN ct_service_methods_units u ON u.id=r.units_id WHERE r.id=1 AND u.id=1 AND r.rates>0 AND u.status='E'",
      'booking_count'=>"SELECT COUNT(*) FROM ct_bookings",
      'order_count'=>"SELECT COUNT(*) FROM ct_order_client_info",
      'ledger_count'=>"SELECT COUNT(*) FROM titan_booking_requests",
      'ledger_order_count'=>"SELECT COUNT(*) FROM titan_booking_orders",
      'staff_service'=>"SELECT service_ids FROM ct_admin_info WHERE id=1",
    ];
    $rows=[];
    foreach ($queries as $key=>$query) {
        $value=$db->query($query)->fetchColumn();
        if ($value===false) throw new RuntimeException('SQL returned no result for '.$key);
        $rows[$key]=$key==='staff_service'?(string)$value:(int)$value;
    }
    echo json_encode(LiveSeedContract::verify($tables,$rows),JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES)."\n";
} catch (Throwable $e) {
    fwrite(STDERR,'BLOCKED: disposable staging SQL readback failed: '.$e->getMessage()."\n");
    exit(78);
}
