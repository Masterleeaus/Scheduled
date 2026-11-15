<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPriceDecimal,BookingQuantityPolicy,BookingRateRule,BookingRateTable,BookingServicePriceSnapshot,BookingPriceInput,BookingPriceAuthority,BookingPriceReader,BookingPriceAudit,BookingReadyGate,BookingCapabilityMatrix,BookingSchemaInspector,BookingEnvironmentCheck,BookingQuoteEvidence};
function ok(bool $x):void {if(!$x)throw new RuntimeException("Assertion failed");}
function bad(callable $f):void{$thrown=false;try{$f();}catch(DomainException|InvalidArgumentException $e){$thrown=true;}if(!$thrown)throw new RuntimeException("Expected reject");}
$x=['service_id'=>2,'method_id'=>1,'method_unit_id'=>3,'quantity'=>2];$u=['id'=>3,'service_id'=>2,'method_id'=>1,'min'=>1,'max'=>5,'base_cents'=>800];$r=[['rule'=>'E','units'=>2,'rate_cents'=>400]];ok(BookingPriceAuthority::verify($x,$u,$r)['total_cents']===1200);
echo "PASS ".basename(__FILE__)."\n";
