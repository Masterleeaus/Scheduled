<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Additive, tenant-local MySQL tables. Do not alter vendor Cleanto source. */
final class BookingWriteSchema {
 public static function statements():array {return [
  'CREATE TABLE IF NOT EXISTS titan_booking_orders (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB',
  'CREATE TABLE IF NOT EXISTS titan_booking_requests (request_key VARCHAR(96) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY, fingerprint CHAR(64) CHARACTER SET ascii NOT NULL, state VARCHAR(16) NOT NULL, booking_id BIGINT UNSIGNED DEFAULT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB',
  'CREATE INDEX titan_bookings_staff_time ON ct_bookings (booking_date_time, booking_status)',
  'CREATE INDEX titan_order_client_order_id ON ct_order_client_info (order_id)'
 ];}
 public static function baseTables():array {return array_slice(self::statements(),0,2);}
 public static function optionalIndexes():array {return array_slice(self::statements(),2);}
}
