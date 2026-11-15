<?php
declare(strict_types=1);
require dirname(__DIR__, 2).'/private/booking-sidecar/bootstrap.php';
require dirname(__DIR__, 2).'/core/app/Booking/BookingEnvelopeSigner.php';
use Titan\BookingSidecar\Envelope;
use Titan\BookingSidecar\TenantSecrets;
use App\Booking\BookingEnvelopeSigner;
$pass = 0;
function check(bool $ok, string $label): void { global $pass; if (!$ok) throw new RuntimeException('FAIL '.$label); $pass++; echo "PASS $label\n"; }
$key = str_repeat('a', 32); $secret = str_repeat('S', 40); $nonce = str_repeat('c',32); $body='{"operation":"services","payload":{}}'; $ts=1900000000;
$sig=BookingEnvelopeSigner::sign($secret,$key,$body,$ts,$nonce);
$headers=['x-booking-time'=>(string)$ts,'x-booking-nonce'=>$nonce,'x-booking-signature'=>$sig];
check(Envelope::verify($secret,$key,$body,$headers,$ts)===$nonce,'signed request compatible with Laravel');
foreach ([['wrong-key',$body,$headers,$ts],[$key,$body.'x',$headers,$ts],[$key,$body,$headers,$ts+70],[$key,$body,['x-booking-time'=>(string)$ts,'x-booking-nonce'=>$nonce,'x-booking-signature'=>str_repeat('0',64)],$ts]] as $i=>$test) {
 try { Envelope::verify($secret,...$test); throw new RuntimeException('accepted attack'); } catch (InvalidArgumentException $e) { check(true,'tamper/expiry case '.$i); }
}
$temp=tempnam(sys_get_temp_dir(),'tenant_fixture_');
file_put_contents($temp,json_encode([$key=>['enabled'=>true,'dsn'=>'mysql:host=db.internal;port=3306;dbname=booking_a;charset=utf8mb4','db_user'=>'a','db_password'=>'b'],str_repeat('b',32)=>['enabled'=>false,'dsn'=>'mysql:host=db.internal;port=3306;dbname=booking_b;charset=utf8mb4','db_user'=>'b','db_password'=>'b']]));
try {
 $r=new TenantSecrets($temp);
 check($r->get($key)['db_user']==='a','correct tenant credentials');
 foreach ([str_repeat('b',32),'arbitrary-key'] as $bad) {try{$r->get($bad); throw new RuntimeException('accepted tenant');}catch(RuntimeException $e){ if ($e->getMessage()==='accepted tenant') throw $e; check(true,'reject disabled/unknown tenant');}}
} finally {unlink($temp);}
check(class_exists(Titan\BookingSidecar\Dispatcher::class),'sidecar autoloader');
echo "TOTAL PASS $pass\n";
