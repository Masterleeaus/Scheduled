<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingPriceDecimal {
 public static function cents(mixed $value):int {
  if(!is_string($value)&&!is_int($value)) throw new \InvalidArgumentException('Decimal string required');
  $s=(string)$value;
  if(!preg_match('/^(?:0|[1-9][0-9]{0,7})(?:\.[0-9]{1,2})?$/D',$s))throw new \InvalidArgumentException('Invalid money');
  [$whole,$fraction]=array_pad(explode('.',$s,2),2,'');return (int)$whole*100+(int)str_pad($fraction,2,'0');
 }
 public static function display(int $cents):string {if($cents<0)throw new \InvalidArgumentException('Negative amount');return intdiv($cents,100).'.'.str_pad((string)($cents%100),2,'0',STR_PAD_LEFT);}
}
