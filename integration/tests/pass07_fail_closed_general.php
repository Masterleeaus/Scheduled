<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPriceDecimal,BookingQuantityPolicy,BookingRateRule,BookingRateTable,BookingServicePriceSnapshot,BookingPriceInput,BookingPriceAuthority,BookingPriceReader,BookingPriceAudit,BookingReadyGate,BookingCapabilityMatrix,BookingSchemaInspector,BookingEnvironmentCheck,BookingQuoteEvidence};
function ok(bool $x):void {if(!$x)throw new RuntimeException("Assertion failed");}
function bad(callable $f):void{$thrown=false;try{$f();}catch(DomainException|InvalidArgumentException $e){$thrown=true;}if(!$thrown)throw new RuntimeException("Expected reject");}
$r=BookingRateTable::normalize([['rules'=>'G','units'=>'2','rates'=>'4.00']]);bad(fn()=>BookingRateTable::matchExact($r,2));
echo "PASS ".basename(__FILE__)."\n";
