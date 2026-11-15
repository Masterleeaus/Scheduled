<?php
declare(strict_types=1);
require __DIR__.'/LiveSeedContract.php';
use Titan\Staging\Pass113\LiveSeedContract;
$required=['ct_bookings','ct_order_client_info','ct_services','ct_services_method','ct_service_methods_units',
 'ct_services_methods_units_rate','ct_admin_info','ct_week_days_available','titan_booking_requests','titan_booking_orders'];
$tables=array_fill_keys($required,'InnoDB');
for($i=0;$i<28;$i++) $tables['ct_synthetic_other_'.$i]='InnoDB';
$rows=['service'=>1,'method'=>1,'unit'=>1,'rate'=>1,'staff'=>1,'schedule_days'=>5,
 'schedule_distinct_days'=>5,'schedule_total_rows'=>5,'schedule_bad_rows'=>0,
 'linked_methods'=>1,'linked_units'=>1,'linked_rates'=>1,'staff_service'=>'1',
 'booking_count'=>0,'order_count'=>0,'ledger_count'=>0,'ledger_order_count'=>0];
$passed=0;
$mustReject=function(array $t,array $r,string $case) use (&$passed):void {
    try {LiveSeedContract::verify($t,$r);} catch (RuntimeException $e) {$passed++;return;}
    throw new RuntimeException('Rejected fixture was accepted: '.$case);
};
if(LiveSeedContract::verify($tables,$rows)['status']!=='LIVE_SEED_READBACK_OK') throw new RuntimeException('valid fixture rejected');
$passed++;
foreach($required as $table) {$bad=$tables; unset($bad[$table]);$mustReject($bad,$rows,'missing '.$table);}
foreach(['service','method','unit','rate','staff','schedule_days','schedule_distinct_days','schedule_total_rows',
 'linked_methods','linked_units','linked_rates','staff_service','booking_count','ledger_count','order_count','ledger_order_count'] as $key) {
    $bad=$rows;$bad[$key]=($key==='schedule_days'||$key==='schedule_distinct_days'||$key==='schedule_total_rows'?4:
      ($key==='staff_service'?'2':(str_contains($key,'count')||str_contains($key,'ledger_')?1:0)));
    $mustReject($tables,$bad,'invalid '.$key);
}
foreach(['schedule_bad_rows'=>1,'schedule_distinct_days'=>4,'schedule_total_rows'=>6,
 'order_count'=>1,'ledger_order_count'=>1] as $key=>$value) {$bad=$rows;$bad[$key]=$value;$mustReject($tables,$bad,$key);}
$bad=$tables;$bad['ct_bookings']='MyISAM';$mustReject($bad,$rows,'MyISAM');
$bad=$tables;$bad['extra_table']='InnoDB';$mustReject($bad,$rows,'extra table');
echo "PASS114 live-seed contract {$passed} assertions passed (pure component, no database connection).\n";
