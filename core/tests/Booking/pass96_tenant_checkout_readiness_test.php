<?php
 declare(strict_types=1);
 $root=dirname(__DIR__, 2);
 spl_autoload_register(static function (string $name) use ($root): void {
     if (strpos($name, 'App\\') !== 0) return;
     $path=$root.'/app/'.str_replace('\\','/',substr($name,4)).'.php';
     if (is_file($path)) require_once $path;
 });
 use App\Booking\Public\Checkout\CheckoutTenantReadiness as Ready;
 function ensure(bool $x, string $message): void {if (!$x)throw new RuntimeException($message);}
 $tenant=['owner_id'=>7,'booking_writes_enabled'=>true,'booking_schema_verified'=>true,
 'booking_rate_mapping_verified'=>true,'booking_concurrency_verified'=>true,'verified_pricing_settings'=>[]];
 $evidence=['public_writes_enabled'=>true,'mysql_lifecycle_passed'=>true,'concurrency_passed'=>true,
 'pricing_parity_passed'=>true,'tenant_isolation_passed'=>true,'schema_passed'=>true,
 'same_database_transaction'=>true,'independent_persisted_readback'=>true,'staff_locks_held_until_commit'=>true];
 $profiles=[7=>['connection'=>'cleanto_test_7','tenant'=>$tenant,'release_evidence'=>$evidence]];
 $n=0;
 function testReady(bool $value,bool $expected,string $name):void {global $n;ensure($value===$expected,$name);$n++;}
 testReady(Ready::eligible(true,true,true,7,$profiles),true,'configured owner');
 testReady(Ready::eligible(true,true,true,8,$profiles),false,'other owner');
 testReady(Ready::eligible(false,true,true,7,$profiles),false,'global off');
 testReady(Ready::eligible(true,false,true,7,$profiles),false,'unbound executor');
 testReady(Ready::eligible(true,true,false,7,$profiles),false,'missing MySQL driver');
 testReady(Ready::eligible(true,true,true,0,$profiles),false,'invalid owner');
 testReady(Ready::eligible(true,true,true,7,[]),false,'no profiles');
 testReady(Ready::eligible(true,true,true,7,'bad'),false,'wrong profile type');
 $bad=$profiles;$bad[7]['release_evidence']['concurrency_passed']=false;
 testReady(Ready::eligible(true,true,true,7,$bad),false,'failed evidence');
 $bad=$profiles;$bad[7]['tenant']['owner_id']=8;
 testReady(Ready::eligible(true,true,true,7,$bad),false,'tenant mismatch');
 testReady(Ready::eligibleForHost(true,true,true,fn()=>7,$profiles),true,'good hostname mapping');
 testReady(Ready::eligibleForHost(true,true,true,fn()=>8,$profiles),false,'other hostname mapping');
 testReady(Ready::eligibleForHost(true,true,true,fn()=> '7',$profiles),false,'string owner disallowed');
 testReady(Ready::eligibleForHost(true,true,true,function(){throw new RuntimeException('db unavailable');},$profiles),false,'host lookup failure');
 testReady(Ready::eligibleForHost(false,true,true,function(){throw new RuntimeException('should not call');},$profiles),false,'disabled fast reject');
 $controller=file_get_contents($root.'/app/Http/Controllers/Front/BookingPublicSubmissionController.php');
 $view=file_get_contents($root.'/resources/views/front/partials/titan-booking-widget.blade.php');
 ensure(strpos($controller,'CheckoutTenantReadiness::eligible(')!==false,'controller readiness');$n++;
 ensure(strpos($view,'CheckoutTenantReadiness::eligibleForHost(')!==false,'widget readiness');$n++;
 ensure(strpos($controller,'CheckoutSubmissionGate::dispatch')>strpos($controller,'CheckoutTenantReadiness::eligible('),'readiness before writer');$n++;
 echo "Pass 96: $n assertions passed; no MariaDB booking transaction claimed\n";
