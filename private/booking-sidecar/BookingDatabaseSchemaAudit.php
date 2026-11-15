<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Read-only MySQL schema inspection; no auto-migrations against live tenant DB. */
final class BookingDatabaseSchemaAudit {
 public function __construct(private \PDO $db){}
 public function inspect():array {
  BookingDriverGuard::assertMySql($this->db);
  $columns=[];
  $sql="SELECT TABLE_NAME,COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('ct_bookings','ct_order_client_info','ct_service_methods_units','ct_services_methods_units_rate','titan_booking_requests','titan_booking_orders')";
  foreach($this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC) as $row)$columns[$row['TABLE_NAME']][]=$row['COLUMN_NAME'];
  $sql="SELECT TABLE_NAME,INDEX_NAME,SEQ_IN_INDEX,COLUMN_NAME,NON_UNIQUE FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('ct_bookings','titan_booking_requests','titan_booking_orders')";
  $indexes=BookingIndexMetadata::normalize($this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC));
  $engine=[];
  $sql="SELECT TABLE_NAME,ENGINE FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('ct_bookings','ct_order_client_info','titan_booking_requests','titan_booking_orders')";
  foreach($this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC) as $row)$engine[$row['TABLE_NAME']]=$row['ENGINE'];
  return BookingSchemaAuditResult::evaluate($columns,$indexes,$engine);
 }
}
