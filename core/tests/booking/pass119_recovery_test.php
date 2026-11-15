<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout {
 class CheckoutNativeCustomer {public static function map(array $x):array{return ['identity'=>['name'=>'Test','email'=>'test@example.test','phone'=>'0400000000'],'personal_info'=>[]];}}
 class CheckoutCommitCompatibility {public static function assert(array $s,array $c,string $n):void {if(!isset($c['email']))throw new \InvalidArgumentException('Invalid customer');}}
}
namespace App\Booking\Public\Redemption {
 class CommitLegacyClaimKey {public static function derive(string $n):string{return hash('sha256','titan-booking-claim-v1:'.$n);}}
 class CommitLegacyFingerprint {public static function make(int $o,array $s,array $c,array $p=[]):string{return hash('sha256',json_encode([$o,$s,$c,$p]));}}
}
namespace {
 require_once __DIR__.'/../../app/Booking/Public/Checkout/CheckoutRecovery.php';
 use App\Booking\Public\Checkout\CheckoutRecovery;
 use App\Booking\Public\Redemption\CommitLegacyClaimKey;
 use App\Booking\Public\Redemption\CommitLegacyFingerprint;
 $n=str_repeat('a',32);$owner=7;$selection=['service_id'=>1];$customer=['email'=>'test@example.test'];
 $key=CommitLegacyClaimKey::derive($n);
 $hash=CommitLegacyFingerprint::make(7,$selection,['name'=>'Test','email'=>'test@example.test','phone'=>'0400000000'],[]);
 $passed=0;
 $check=static function($ok)use(&$passed){if(!$ok)throw new \RuntimeException('Recovery assertion failed');$passed++;};
 $unknown=CheckoutRecovery::lookup($owner,$n,$selection,$customer,static fn()=>null);
 $check($unknown['status']==='unknown' && $unknown['confirmed']===false && $unknown['resolved']===false);
 $row=['claim_key'=>$key,'owner_id'=>7,'fingerprint'=>$hash,'state'=>'committed','booking_id'=>18,'booking_verified'=>true,'order_verified'=>true,'booking_status'=>'C'];
 $yes=CheckoutRecovery::lookup($owner,$n,$selection,$customer,static fn()=>$row);
 $check($yes['confirmed']===true && $yes['booking_reference']==='18' && !$yes['payment_settled']);
 foreach([['fingerprint'=>str_repeat('f',64)],['owner_id'=>8],['state'=>'claimed'],['booking_verified'=>false],['order_verified'=>false],['booking_id'=>0]] as $change){
  try{CheckoutRecovery::lookup($owner,$n,$selection,$customer,static fn()=>array_replace($row,$change));throw new \RuntimeException('Accepted fake evidence');}
  catch(\RuntimeException $e){$check($e->getMessage()!=='Accepted fake evidence');}
 }
 try { CheckoutRecovery::lookup(0,$n,$selection,$customer,static fn()=>null);throw new \RuntimeException('Accepted tenant zero'); }
 catch(\InvalidArgumentException $e){$check(true);}
 echo "Pass 119 recovery assertions: $passed passed\n";
}
