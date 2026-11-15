<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingPriceAuthority {
 public static function verify(array $input,array $snapshot,array $rates):array {
  $request=BookingPriceInput::parse($input);
  if($request['method_unit_id']!==$snapshot['id']||$request['service_id']!==$snapshot['service_id']||$request['method_id']!==$snapshot['method_id'])throw new \DomainException('Mismatched service selection');
  $qty=BookingQuantityPolicy::check($request['quantity'],$snapshot['min'],$snapshot['max']);
  // Only exact rate tables are accepted; generalized rules need parity testing against Cleanto.
  $unit=BookingRateTable::matchExact($rates,$qty);$base=$snapshot['base_cents'];$total=$base+$unit;
  if($total>999999999)throw new \DomainException('Price overflow');return ['base_cents'=>$base,'rate_cents'=>$unit,'total_cents'=>$total,'quantity'=>$qty,'verified_scope'=>'exact_rule_no_addons'];
 }
}
