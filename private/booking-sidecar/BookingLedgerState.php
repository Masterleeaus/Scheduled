<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Reject unexpected database states instead of treating them as a pending command. */
final class BookingLedgerState {
 public static function read(array $row):string {
  $state=$row['state']??$row['status']??null;
  if(!is_string($state)||!in_array($state,['pending','completed'],true))throw new \DomainException('Invalid idempotency ledger state');
  if(isset($row['state'],$row['status'])&&$row['state']!==$row['status'])throw new \DomainException('Inconsistent ledger state');
  if($state==='completed'&&array_key_exists('booking_id',$row))BookingPositiveId::parse($row['booking_id']);
  return $state;
 }
}
