<?php
declare(strict_types=1);
require __DIR__.'/../../app/Booking/Public/Checkout/CheckoutPaymentRecord.php';
use App\Booking\Public\Checkout\CheckoutPaymentRecord as R;
$n=0;
function check130(bool $condition,string $message):void { global $n; if(!$condition)throw new RuntimeException($message);$n++; }
function row130():array {return ['id'=>7,'order_id'=>81,'payment_status'=>'Completed','payment_method'=>'Offline','transaction_id'=>'','amount'=>'42.50','net_amount'=>'42.50','payment_date'=>'2026-11-10'];}
function status130(array $changes):string{return R::classify([array_replace(row130(),$changes)],81)['record_status'];}
check130(status130([])==='completed_recorded','Valid Cleanto row must remain readable');
foreach (['0','0.00','0.0','-1.00','+5.00','1e3','1E3',' 5.00','5.00 ','01.00','1.234','1.0000001','NaN','INF','9999999999999.99',null,true,[],new stdClass()] as $invalid) {
 check130(status130(['amount'=>$invalid])==='ambiguous','Invalid gross amount accepted: '.get_debug_type($invalid));
 check130(status130(['net_amount'=>$invalid])==='ambiguous','Invalid net amount accepted: '.get_debug_type($invalid));
}
foreach (['1','0.01','1.2','1.20',1,1.2,'999999999999.99'] as $valid) {
 check130(status130(['amount'=>$valid])==='completed_recorded','Valid gross amount rejected');
 check130(status130(['net_amount'=>$valid])==='completed_recorded','Valid net amount rejected');
}
foreach (['2026-02-30','2025-02-29','0000-00-00','2026-13-10','2026-11-31','2026-1-10','2026-11-1','2026-11-10 00:00:00','',null,20261110,[],new stdClass()] as $invalid) {
 check130(status130(['payment_date'=>$invalid])==='ambiguous','Invalid payment date accepted');
}
foreach (['2024-02-29','2026-11-10','1999-12-31'] as $valid)check130(status130(['payment_date'=>$valid])==='completed_recorded','Valid date rejected');
check130(R::classify([row130()],81)['payment_settled']===false,'Accounting evidence became settlement assertion');
check130(R::classify([array_replace(row130(),['payment_date'=>'0000-00-00'])],81)['payment_settled']===false,'Invalid date became settlement assertion');
echo "Pass130 strict payment evidence: $n assertions passed\n";
