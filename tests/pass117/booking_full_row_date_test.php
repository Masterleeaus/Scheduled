<?php
declare(strict_types=1);
require_once __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\BookingWriteProofRows;
use Titan\BookingSidecar\BookingPersonalInfo;
$start='2026-12-01 10:00:00';
$command=['service'=>['service_id'=>2,'method_id'=>3,'method_unit_id'=>4,'quantity'=>2], 'staff_ids'=>[7], 'duration_minutes'=>120, 'window'=>['local_start'=>$start], 'customer'=>['name'=>'Jane Example','email'=>'jane@example.org','phone'=>'0400000000'], 'personal_info'=>['address'=>'12 Example Lane','city'=>'Melbourne','state'=>'VIC','zip'=>'3000','notes'=>'Side entry']];
$booking=['id'=>'17','order_id'=>'29','client_id'=>'31','order_date'=>'2026-12-01','booking_date_time'=>$start,'service_id'=>'2','method_id'=>'3','method_unit_id'=>'4','method_unit_qty'=>'2','method_unit_qty_rate'=>'50.00','booking_status'=>'A','staff_ids'=>'7'];
$order=['id'=>'31','order_id'=>'29','client_name'=>'Jane Example','client_email'=>'jane@example.org','client_phone'=>'0400000000','order_duration'=>'120','client_personal_info'=>BookingPersonalInfo::encode($command['personal_info'])];
$checks=0;
BookingWriteProofRows::check($booking,$order,17,$command,'50.00');$checks++;
foreach(['2026-11-30','2026-12-02','2026-12-01 00:00:00',''] as $bad){$row=$booking;$row['order_date']=$bad;try{BookingWriteProofRows::check($row,$order,17,$command,'50.00');throw new RuntimeException('Incorrect order date accepted');}catch(DomainException $e){$checks++;}}
$row=$booking;unset($row['order_date']);try{BookingWriteProofRows::check($row,$order,17,$command,'50.00');throw new RuntimeException('Missing date accepted');}catch(DomainException $e){$checks++;}
$order['client_personal_info']=BookingPersonalInfo::encode(['address'=>'Changed']);try{BookingWriteProofRows::check($booking,$order,17,$command,'50.00');throw new RuntimeException('Mismatched location accepted');}catch(DomainException $e){$checks++;}
echo "PASS117 complete-row: $checks assertions passed\n";
