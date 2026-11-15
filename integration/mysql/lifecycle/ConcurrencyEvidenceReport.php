<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyEvidenceReport {
 public static function attach(array $report,array $evidence):array {
  if(($evidence['database_readback']['persisted']??false)!==true || ($evidence['database_readback']['overlap_scan_passed']??false)!==true)throw new \RuntimeException('No committed booking evidence');
  $report['database_evidence']=$evidence['database_readback'];return $report;
 }
}
