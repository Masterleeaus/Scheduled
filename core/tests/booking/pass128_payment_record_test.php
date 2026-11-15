<?php
declare(strict_types=1);
require __DIR__.'/../../app/Booking/Public/Checkout/CheckoutPaymentRecord.php';
use App\Booking\Public\Checkout\CheckoutPaymentRecord;
class Statement128 extends PDOStatement {
 public function __construct(public array $rows){}
 public function execute(?array $params=null):bool {if($params!==[51])throw new RuntimeException('Order not parameterized');return true;}
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT,...$args):array {return $this->rows;}
}
class Connection128 extends PDO {
 public array $sql=[];
 public function __construct(public array $rows){}
 public function inTransaction():bool{return false;}
 public function prepare(string $query,array $options=[]):PDOStatement|false{$this->sql[]=$query;return new Statement128($this->rows);}
}
$n=0;
function check128(bool $pass,string $message):void {global $n; if(!$pass)throw new RuntimeException($message);$n++;}
function row128(int $id,string $status):array{return ['id'=>$id,'order_id'=>51,'payment_status'=>$status,'payment_method'=>'Offline','transaction_id'=>'','amount'=>'10.00','net_amount'=>'10.00','payment_date'=>'2026-11-10'];}
$cases=[ [[], 'none'],[[row128(1,'Pending')],'pending'],[[row128(1,'Completed')],'completed_recorded'],[[row128(1,'Pending'),row128(2,'Completed')],'mixed'],[[row128(1,'bogus')],'ambiguous'] ];
foreach($cases as [$rows,$expected]){
 $db=new Connection128($rows);$actual=CheckoutPaymentRecord::inspect($db,51);
 check128($actual['record_status']===$expected,'Incorrect Cleanto record status');
 check128($actual['record_count']===count($rows),'Wrong record count');
 check128($actual['payment_settled']===false,'Legacy payment record incorrectly treated as settled');
 check128(str_contains($db->sql[0],'FROM ct_payments WHERE order_id=?'),'Missing parameterized scoped query');
}
foreach ([[row128(1,'Completed'),row128(1,'Completed')],[array_replace(row128(1,'Completed'),['order_id'=>52])],[array_replace(row128(1,'Completed'),['id'=>0])]] as $bad) {
 try {CheckoutPaymentRecord::classify($bad,51);throw new RuntimeException('Invalid payment accepted');}
 catch(RuntimeException $e){check128($e->getMessage()!=='Invalid payment accepted','Invalid payment row not rejected');}
}
try{CheckoutPaymentRecord::classify([],0);throw new RuntimeException('Bad ID accepted');}catch(InvalidArgumentException $e){check128(true,'Bad ID rejected');}
$many=array_fill(0,101,row128(1,'Pending'));$db=new Connection128($many);
try{CheckoutPaymentRecord::inspect($db,51);throw new RuntimeException('Unbounded rows accepted');}catch(RuntimeException $e){check128($e->getMessage()!=='Unbounded rows accepted','Row cap not enforced');}
echo "Pass128 payment record: $n assertions passed\n";
