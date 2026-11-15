<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout {
 final class CheckoutNativeCustomer {public static function map(array $customer):array{return ['identity'=>['name'=>'Test','email'=>'person@example.test','phone'=>'0400000000'],'personal_info'=>[]];}}
 final class CheckoutCommitCompatibility {public static function assert(array $s,array $c,string $n):void {}}
}
namespace App\Booking\Public\Redemption {
 final class CommitLegacyClaimKey {public static function derive(string $nonce):string{return hash('sha256','titan-booking-claim-v1:'.$nonce);}}
 final class CommitLegacyFingerprint {public static function make(int $owner,array $s,array $i,array $p):string{return hash('sha256',json_encode([$owner,$s,$i,$p]));}}
 final class CommitSelection {public static function parse(array $selection):array{return $selection;}}
 final class CommitLegacyProof {public static function verify(array $row,array $selection):void {if($row['service_id']!=$selection['service_id'])throw new \RuntimeException('Selection mismatch');}}
}
namespace {
 require __DIR__.'/../../../private/booking-sidecar/BookingPersonalInfo.php';
 require __DIR__.'/../../app/Booking/Public/Checkout/CheckoutRecovery.php';
 require __DIR__.'/../../app/Booking/Public/Checkout/CheckoutPaymentRecord.php';
 require __DIR__.'/../../app/Booking/Public/Checkout/CheckoutRecoveryReader.php';
 use App\Booking\Public\Checkout\CheckoutRecovery;
 use App\Booking\Public\Checkout\CheckoutRecoveryReader;
 use App\Booking\Public\Redemption\CommitLegacyClaimKey;
 use App\Booking\Public\Redemption\CommitLegacyFingerprint;
 class Statement121 extends \PDOStatement {
  public array $results; public bool $ok=true;
  public function __construct(array $rows){$this->results=$rows;}
  public function execute(?array $params=null):bool{return $this->ok;}
  public function fetchAll(int $mode=\PDO::FETCH_DEFAULT,...$args):array{return $this->results;}
 }
 class Connection121 extends \PDO {
  public array $queries=[]; public array $results=[];
  public function __construct(array $results){$this->results=$results;}
  public function inTransaction():bool{return false;}
  public function prepare(string $query,array $options=[]):\PDOStatement|false {
   $this->queries[]=$query;return new Statement121(array_shift($this->results)??[]);
  }
 }
 $assertions=0;
 function assert121(bool $ok,string $message):void {global $assertions;if(!$ok)throw new \RuntimeException($message);$assertions++;}
 $owner=7;$nonce=str_repeat('a',32);$selection=['service_id'=>4,'date_time'=>'2026-11-17 09:00:00'];$customer=['email'=>'person@example.test'];
 $key=CommitLegacyClaimKey::derive($nonce);
 $hash=CommitLegacyFingerprint::make($owner,$selection,['name'=>'Test','email'=>'person@example.test','phone'=>'0400000000'],[]);
 $claim=['claim_key'=>$key,'owner_id'=>7,'fingerprint'=>$hash,'state'=>'committed','booking_id'=>21];
 $booking=['id'=>21,'order_id'=>51,'client_id'=>3,'service_id'=>4,'order_date'=>'2026-11-17','booking_status'=>'C'];
 foreach(['A'=>'confirmed','C'=>'confirmed','CO'=>'completed','RS'=>'rescheduled','CC'=>'cancelled','CS'=>'cancelled','R'=>'rejected','MN'=>'no_show'] as $native=>$expected){
  $pdo=new Connection121([[$claim],[array_replace($booking,['booking_status'=>$native])],[['id'=>3,'order_id'=>51,'client_name'=>'Test','client_email'=>'person@example.test','client_phone'=>'0400000000','client_personal_info'=>\Titan\BookingSidecar\BookingPersonalInfo::encode([])]]]);
  $proof=CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,['name'=>'Test','email'=>'person@example.test','phone'=>'0400000000'],[]);
  $result=CheckoutRecovery::lookup($owner,$nonce,$selection,$customer,static fn()=>$proof);
  assert121($result['status']===$expected,"Incorrect status for {$native}");
  assert121($result['confirmed']===($expected==='confirmed'),"False confirmation for {$native}");
  assert121($result['resolved']===true && $result['payment_settled']===false,'Incorrect result flags');
  assert121(str_contains($pdo->queries[2],'FROM ct_order_client_info') && !str_contains($pdo->queries[2],'FROM ct_orders'),'Not using actual Cleanto order table');
 }
 foreach([[],[['id'=>3,'order_id'=>52]], [['id'=>3,'order_id'=>51],['id'=>3,'order_id'=>51]]] as $orderRows){
  $pdo=new Connection121([[$claim],[$booking],$orderRows]);
  try{CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,['name'=>'Test','email'=>'person@example.test','phone'=>'0400000000'],[]);throw new \RuntimeException('Accepted broken order proof');}
  catch(\RuntimeException $e){assert121($e->getMessage()!=='Accepted broken order proof','Failed to reject invalid order');}
 }
 $pdo=new Connection121([[$claim],[$booking],[['id'=>3,'order_id'=>51,'client_name'=>'Test','client_email'=>'person@example.test','client_phone'=>'0400000000','client_personal_info'=>\Titan\BookingSidecar\BookingPersonalInfo::encode([])]]]);
 assert121(CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,['name'=>'Test','email'=>'person@example.test','phone'=>'0400000000'],[])['booking_status']==='C','Status not persisted');
 $expected=['name'=>'Test','email'=>'person@example.test','phone'=>'0400000000'];
 $goodOrder=['id'=>3,'order_id'=>51,'client_name'=>'Test','client_email'=>'person@example.test','client_phone'=>'0400000000','client_personal_info'=>\Titan\BookingSidecar\BookingPersonalInfo::encode([])];
 $pdo=new Connection121([[$claim],[$booking],[$goodOrder]]);
 assert121(CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$expected,[])['order_verified']===true,'Valid matching customer rejected');
 foreach (['client_name'=>'Other Name','client_email'=>'wrong@example.test','client_phone'=>'0411111111'] as $field=>$wrong) {
  $pdo=new Connection121([[$claim],[$booking],[array_replace($goodOrder,[$field=>$wrong])]]);
  try {CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$expected,[]);throw new \RuntimeException('Accepted wrong order customer');}
  catch (\RuntimeException $e) {assert121($e->getMessage()!=='Accepted wrong order customer','Did not reject '.$field);}
 }
 $pdo=new Connection121([[$claim],[$booking],[['id'=>3,'order_id'=>51,'client_name'=>'Test','client_email'=>'person@example.test']]]);
 try {CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$expected,[]);throw new \RuntimeException('Accepted incomplete order identity');}
 catch (\RuntimeException $e) {assert121($e->getMessage()!=='Accepted incomplete order identity','Missing customer field passed');}
 // Proof must reject dates that do not match the selected local appointment day.
 foreach (['2026-11-18','0000-00-00',null] as $badDate) {
  $badBooking=array_replace($booking,['order_date'=>$badDate]);
  $pdo=new Connection121([[$claim],[$badBooking],[$goodOrder]]);
  try {CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$expected,[]);throw new \RuntimeException('Accepted wrong order date');}
  catch (\RuntimeException $e) {assert121($e->getMessage()!=='Accepted wrong order date','Invalid order date accepted');}
 }
 // A missing identity cannot silently disable the customer ownership check.
 foreach ([[],['name'=>'Test','email'=>'person@example.test'],['name'=>'','email'=>'person@example.test','phone'=>'0400000000']] as $badIdentity) {
  $pdo=new Connection121([[$claim],[$booking],[$goodOrder]]);
  try {CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$badIdentity,[]);throw new \RuntimeException('Accepted incomplete identity');}
  catch (\InvalidArgumentException $e) {assert121(true,'Incomplete identity rejected');}
 }
 $seen=null;
 $result=CheckoutRecovery::lookup($owner,$nonce,$selection,$customer,static function($k,$o,$h,$sel,$identity,$personal)use(&$seen,$claim){$seen=[$identity,$personal];return $claim+['booking_verified'=>true,'order_verified'=>true,'booking_status'=>'C'];});
 assert121($seen===[$expected,[]] && $result['confirmed']===true,'Validated identity not forwarded to reader');
 // Pass 125: a matching order number is insufficient without the actual Cleanto
 // foreign-key pair (bookings.client_id -> order_client_info.id).
 foreach ([
   ['id'=>9],
   ['id'=>null],
   ['client_personal_info'=>base64_encode('corrupt')],
   ['client_personal_info'=>null],
 ] as $broken) {
  $pdo=new Connection121([[$claim],[$booking],[array_replace($goodOrder,$broken)]]);
  try {CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$expected,[]);throw new \RuntimeException('Accepted wrong client link or personal information');}
  catch (\RuntimeException $e) {assert121($e->getMessage()!=='Accepted wrong client link or personal information','Mismatched linked customer row passed');}
 }
 // Stored address verification is strict, including city/state/postcode and notes.
 $personal=['zip'=>'3000','address'=>'7 Test St','city'=>'Melbourne','state'=>'VIC','notes'=>'Doorbell'];
 $stored=\Titan\BookingSidecar\BookingPersonalInfo::encode($personal);
 $pdo=new Connection121([[$claim],[$booking],[array_replace($goodOrder,['client_personal_info'=>$stored])]]);
 assert121(CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$expected,$personal)['order_verified']===true,'Correct service address not recovered');
 foreach(['zip'=>'3999','address'=>'8 Test St','city'=>'Sydney','state'=>'NSW','notes'=>'Different'] as $field=>$bad) {
  $pdo=new Connection121([[$claim],[$booking],[array_replace($goodOrder,['client_personal_info'=>$stored])]]);
  try {CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$expected,array_replace($personal,[$field=>$bad]));throw new \RuntimeException('Accepted altered service location');}
  catch (\RuntimeException $e) {assert121($e->getMessage()!=='Accepted altered service location','Altered address field accepted');}
 }
 // Pass 128: the checkout response must surface recorded accounting evidence without claiming settlement.
 foreach (['Pending'=>'pending','Completed'=>'completed_recorded'] as $nativePayment=>$expect) {
  $paymentRow=['id'=>41,'order_id'=>51,'payment_status'=>$nativePayment,'payment_method'=>'Offline','transaction_id'=>'','amount'=>'10','net_amount'=>'10'];
  $pdo=new Connection121([[$claim],[$booking],[$goodOrder],[$paymentRow]]);
  $proof=CheckoutRecoveryReader::read($pdo,$key,$owner,$hash,$selection,$expected,[]);
  assert121($proof['payment_record_status']===$expect,'Payment row was not reconciled');
  assert121(str_contains($pdo->queries[3],'FROM ct_payments WHERE order_id=?'),'Payment query not scoped to Cleanto order');
  $result=CheckoutRecovery::lookup($owner,$nonce,$selection,$customer,static fn()=>$proof);
  assert121($result['payment_record_status']===$expect && $result['payment_settled']===false,'Unsafe payment result');
 }
 echo "Pass 121 native Cleanto recovery: {$assertions} assertions passed\n";
}
