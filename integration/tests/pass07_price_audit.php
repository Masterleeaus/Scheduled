<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPriceDecimal,BookingQuantityPolicy,BookingRateRule,BookingRateTable,BookingServicePriceSnapshot,BookingPriceInput,BookingPriceAuthority,BookingPriceReader,BookingPriceAudit,BookingReadyGate,BookingCapabilityMatrix,BookingSchemaInspector,BookingEnvironmentCheck,BookingQuoteEvidence};
function ok(bool $x):void {if(!$x)throw new RuntimeException("Assertion failed");}
function bad(callable $f):void{$thrown=false;try{$f();}catch(DomainException|InvalidArgumentException $e){$thrown=true;}if(!$thrown)throw new RuntimeException("Expected reject");}
ok(count(BookingPriceAudit::findings(['tax_enabled'=>false,'discount_enabled'=>false,'coupon_enabled'=>false,'addons_enabled'=>false,'travel_fee_enabled'=>false,'deposit_enabled'=>false]))===0);ok(count(BookingPriceAudit::findings([]))===6);
echo "PASS ".basename(__FILE__)."\n";
