<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Non-mutating MySQL operational facts; no credentials in report. */
final class BookingDatabaseCapabilityReader {
 public function __construct(private \PDO $db){}
 public function inspect():array {
  $version=(string)$this->db->query('SELECT VERSION()')->fetchColumn();
  $engine=$this->db->query("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='ct_bookings'")->fetchColumn();
  $database=(string)$this->db->query('SELECT DATABASE()')->fetchColumn();
  return ['database_ref'=>BookingDatabaseIdentity::safeReference($database),'server_version'=>$version,'booking_table_innodb'=>strtoupper((string)$engine)==='INNODB'];
 }
}
