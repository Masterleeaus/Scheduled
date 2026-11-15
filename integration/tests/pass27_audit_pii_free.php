<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$a=['id'=>7,'order_id'=>20,'client_id'=>9,'service_id'=>10,'method_id'=>3,'method_unit_id'=>2,'method_unit_qty'=>1,'rate'=>1250,'booking_date_time'=>'2026-12-01 10:00:00','staff_ids'=>[4],'booking_status'=>'A'];$r=\Titan\BookingIntegration\LifecycleAuditReport::successful($a,$a,[]);if(isset($r['client_email'])||$r['payment_settlement_verified']!==false)throw new Exception('misleading evidence');
echo "PASS pass27_audit_pii_free\n";
