<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Audit a set of mutation lock plans before executing any transaction. */
final class BookingMutationLockInventory {
 public static function inspect(string $tenant,array $operations):array {
  $counts=[];
  foreach($operations as $operation=>$keys){
   if(!in_array($operation,['create','cancel','reschedule'],true)||!is_array($keys))throw new \InvalidArgumentException('Unknown lock operation');
   $counts[$operation]=count(BookingTenantLockPolicy::verify($tenant,$keys));
  }
  return $counts;
 }
}
