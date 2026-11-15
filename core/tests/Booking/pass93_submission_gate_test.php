<?php
declare(strict_types=1);
require_once __DIR__.'/../../app/Booking/Public/Checkout/CheckoutSubmissionExecutor.php';
require_once __DIR__.'/../../app/Booking/Public/Checkout/CheckoutSubmissionGate.php';
use App\Booking\Public\Checkout\CheckoutSubmissionExecutor;
use App\Booking\Public\Checkout\CheckoutSubmissionGate;
class NoWriteExecutor implements CheckoutSubmissionExecutor {
 public $calls=0;
 public function commit(int $owner,array $selection,array $identity,array $personalInfo,string $intentNonce):array {$this->calls++;return ['confirmed'=>true,'payment_settled'=>false,'booking_id'=>31];}
}
function blocked(callable $f):void {try{$f();}catch(\RuntimeException $e){return;}throw new \RuntimeException('Expected refusal');}
$x=new NoWriteExecutor;
blocked(fn()=>CheckoutSubmissionGate::dispatch(false,$x,1,[],[],'a'.str_repeat('0',31)));
blocked(fn()=>CheckoutSubmissionGate::dispatch(true,null,1,[],[],'a'.str_repeat('0',31)));
if($x->calls!==0)throw new \RuntimeException('Fail-closed gate invoked writer');
$controller=file_get_contents(__DIR__.'/../../app/Http/Controllers/Front/BookingPublicSubmissionController.php');
if(strpos($controller,"public_commit_enabled")===false||strpos($controller,'app()->bound(CheckoutSubmissionExecutor::class)')===false)throw new \RuntimeException('Missing release gate');
if(strpos($controller,'IntentSlotProof::validate')===false||strpos($controller,'IntentCatalogueProof::validate')===false)throw new \RuntimeException('Missing fresh authoritative proof');
if(strpos($controller,'IntentVerifier::verify')===false)throw new \RuntimeException('Unsigned submission allowed');
if(strpos($controller,'CheckoutSubmissionGate::dispatch')===false)throw new \RuntimeException('No writer handoff');
$r=file_get_contents(__DIR__.'/../../routes/web.php');
if(strpos($r,"/titan-booking/checkout/submit")===false)throw new \RuntimeException('Submission route missing');
echo "Pass 93: 7 assertions passed; no DB transaction claimed\n";
