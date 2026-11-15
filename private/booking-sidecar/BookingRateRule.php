<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingRateRule {
 public static function parse(array $row):array {
  if(!in_array($row['rules']??null,['G','E'],true))throw new \DomainException('Unknown Cleanto rate rule');
  if(!isset($row['units'])||!preg_match('/^[0-9]+$/D',(string)$row['units']))throw new \DomainException('Non-integer rate unit');
  $units=(int)$row['units'];if($units<1||$units>10000)throw new \DomainException('Invalid rate units');
  return ['rule'=>$row['rules'],'units'=>$units,'rate_cents'=>BookingPriceDecimal::cents((string)$row['rates'])];
 }
}
