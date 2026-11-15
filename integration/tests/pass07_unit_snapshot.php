<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPriceDecimal,BookingQuantityPolicy,BookingRateRule,BookingRateTable,BookingServicePriceSnapshot,BookingPriceInput,BookingPriceAuthority,BookingPriceReader,BookingPriceAudit,BookingReadyGate,BookingCapabilityMatrix,BookingSchemaInspector,BookingEnvironmentCheck,BookingQuoteEvidence};
function ok(bool $x):void {if(!$x)throw new RuntimeException("Assertion failed");}
function bad(callable $f):void{$thrown=false;try{$f();}catch(DomainException|InvalidArgumentException $e){$thrown=true;}if(!$thrown)throw new RuntimeException("Expected reject");}
$u=BookingServicePriceSnapshot::fromRow(['id'=>3,'services_id'=>2,'methods_id'=>1,'minlimit'=>1,'maxlimit'=>5,'base_price'=>'8.00','status'=>'E']);ok($u['base_cents']===800);bad(fn()=>BookingServicePriceSnapshot::fromRow(['id'=>3,'services_id'=>2,'methods_id'=>1,'minlimit'=>1,'maxlimit'=>5,'base_price'=>'8.00','status'=>'D']));
echo "PASS ".basename(__FILE__)."\n";
