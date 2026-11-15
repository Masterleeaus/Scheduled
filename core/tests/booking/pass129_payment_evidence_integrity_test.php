<?php
declare(strict_types=1);
require __DIR__.'/../../app/Booking/Public/Checkout/CheckoutPaymentRecord.php';
use App\Booking\Public\Checkout\CheckoutPaymentRecord as Evidence;
$n=0;
function expect129(bool $condition,string $why):void {global $n;if(!$condition)throw new RuntimeException($why);$n++;}
function payment129(int $id=1,string $status='Completed'):array {
 return ['id'=>$id,'order_id'=>51,'payment_status'=>$status,'payment_method'=>'Offline','transaction_id'=>'receipt-'.$id,'amount'=>'125.00','net_amount'=>'125.00','payment_date'=>'2026-11-10'];
}
function state129(array $rows):string{return Evidence::classify($rows,51)['record_status'];}
expect129(state129([payment129()])==='completed_recorded','Valid legacy row rejected');
expect129(state129([payment129(1,'Pending')])==='pending','Pending row misclassified');
expect129(state129([payment129(1),payment129(2,'Pending')])==='mixed','Mixed rows misclassified');
foreach (['amount','net_amount'] as $field) {
 foreach ([null,'',0,-1,'NaN','INF','1e999','not-money',[],new stdClass()] as $value) {
  expect129(state129([array_replace(payment129(),[$field=>$value])])==='ambiguous',"Invalid $field was treated as complete");
 }
}
foreach ([null,''," 	",[]] as $method) {
 expect129(state129([array_replace(payment129(),['payment_method'=>$method])])==='ambiguous','Invalid payment method accepted');
}
foreach ([null,8,[]] as $reference) {
 expect129(state129([array_replace(payment129(),['transaction_id'=>$reference])])==='ambiguous','Invalid transaction reference accepted');
}
$duplicate=payment129(2);$duplicate['transaction_id']='receipt-1';
expect129(state129([payment129(),$duplicate])==='ambiguous','Duplicate transaction reference classified as complete');
$other=payment129(2);$other['transaction_id']='';
$blank=payment129(1);$blank['transaction_id']='';
expect129(state129([$blank,$other])==='completed_recorded','Offline rows without gateway references must be supported');
expect129(state129([])==='none','No records misclassified');
expect129(Evidence::classify([payment129()],51)['payment_settled']===false,'Provider settlement inferred');
foreach ([['order_id'=>52],['id'=>0]] as $invalid) {
 try {Evidence::classify([array_replace(payment129(),$invalid)],51);throw new RuntimeException('Invalid linkage accepted');}
 catch (RuntimeException $e) {expect129($e->getMessage()!=='Invalid linkage accepted','Linkage validation failed');}
}
echo "Pass129 payment integrity: $n assertions passed\n";
