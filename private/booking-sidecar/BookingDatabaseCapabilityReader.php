<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Non-mutating PostgreSQL operational facts; no credentials in report. PostgreSQL tables are always transactional. */
final class BookingDatabaseCapabilityReader {
 public function __construct(private \PDO $db){}
 public function inspect():array {
  $version=(string)$this->db->query('SHOW server_version')->fetchColumn();
  $present=$this->db->query("SELECT 1 FROM information_schema.tables WHERE table_catalog=current_database() AND table_schema=current_schema() AND table_name='ct_bookings'")->fetchColumn()!==false;
  $database=(string)$this->db->query('SELECT current_database()')->fetchColumn();
  return ['database_ref'=>BookingDatabaseIdentity::safeReference($database),'server_version'=>$version,'booking_table_present'=>$present,'transactional_engine'=>true];
 }
}
