<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** SQL intentionally scans every occupied booking in the lookback, not only the winning booking. */
final class PersistedOverlapQuery {
 public const SQL="SELECT b.id,b.booking_date_time,b.staff_ids,b.booking_status,o.order_duration FROM ct_bookings b JOIN ct_order_client_info o ON o.order_id=b.order_id WHERE b.booking_date_time >= ? AND b.booking_date_time < ? AND b.booking_status IN ('A','C','RS') ORDER BY b.booking_date_time,b.id LIMIT 5001";
 public static function bounds(array $window):array {return PersistedWindowBounds::forWindow($window);}
}
