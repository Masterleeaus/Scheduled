<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Read both Cleanto table totals through the independent acceptance connection. */
final class LifecycleFootprintSnapshot {
 public static function read(\PDO $reader):array {
  LifecycleFootprintBoundary::assertCommitted($reader);
  $out=['bookings'=>LifecycleFootprintSql::count($reader,LifecycleFootprintSql::BOOKINGS),
        'orders'=>LifecycleFootprintSql::count($reader,LifecycleFootprintSql::ORDERS)];
  LifecycleFootprintBoundary::assertCommitted($reader);
  return $out;
 }
}
