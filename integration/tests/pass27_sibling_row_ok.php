<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$r=\Titan\BookingIntegration\LifecycleSiblingRow::parse(['id'=>2,'order_id'=>20,'booking_date_time'=>'2026-12-01 09:00:00','staff_ids'=>'4,5','booking_status'=>'A']);if($r['id']!==2||count($r['staff_ids'])!==2)throw new Exception('parse');
echo "PASS pass27_sibling_row_ok\n";
