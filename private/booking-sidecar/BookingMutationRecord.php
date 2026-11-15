<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Normalized, tenant-local legacy booking row. */
final class BookingMutationRecord {
 private static function parseLegacyStaff(string $csv):array {
  if(trim($csv)==='')return [];
  $parts=explode(',',$csv);$ids=[];
  foreach($parts as $part){$part=trim($part);if(!ctype_digit($part)||strlen($part)>10)throw new \DomainException('Malformed legacy staff list');$ids[]=(int)$part;}
  return BookingStaff::parse($ids);
 }
 public static function parse(array $row):array {
  $id=filter_var($row['id']??null,FILTER_VALIDATE_INT);$order=filter_var($row['order_id']??null,FILTER_VALIDATE_INT);
  if(!$id||$id<1||$id>2147483647||!$order||$order<1||$order>2147483647)throw new \DomainException('Invalid legacy booking identity');
  $status=$row['booking_status']??null;
  if(!is_string($status)||!in_array($status,['A','C','RS','R','CC','CS','CO','MN'],true))throw new \DomainException('Unknown legacy status');
  if(!is_string($row['booking_date_time']??null)||!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/D',$row['booking_date_time']))throw new \DomainException('Invalid legacy booking start');
  $stamp=\DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$row['booking_date_time'],new \DateTimeZone('UTC'));
  if(!$stamp||$stamp->format('Y-m-d H:i:s')!==$row['booking_date_time'])throw new \DomainException('Invalid legacy booking calendar date');
  return ['id'=>(int)$id,'order_id'=>(int)$order,'status'=>$status,'staff_ids'=>self::parseLegacyStaff((string)($row['staff_ids']??'')),'start'=>$row['booking_date_time']];
 }
}
