<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Reject calendar-impossible dates and noncanonical timestamps in persisted appointment evidence. */
final class LifecyclePersistedTimestamp {
 public static function parse(mixed $raw):string {
  if(!is_string($raw)||!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D',$raw))throw new \RuntimeException('Invalid stored booking timestamp');
  $value=\DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$raw,new \DateTimeZone('UTC'));
  $errors=\DateTimeImmutable::getLastErrors();
  if(!$value||($errors!==false&&($errors['warning_count']||$errors['error_count']))||$value->format('Y-m-d H:i:s')!==$raw)throw new \RuntimeException('Nonexistent stored booking timestamp');
  $year=(int)$value->format('Y');
  if($year<2000||$year>2099)throw new \RuntimeException('Unsupported stored booking year');
  return $raw;
 }
}
