<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Amounts are calculated in integer cents until legacy persistence boundary. */
final class BookingMoney {
 public static function cents(mixed $value):int {
  if(!is_string($value)&&!is_int($value))throw new InvalidArgumentException('Invalid monetary input');
  $v=(string)$value;if(!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D',$v))throw new InvalidArgumentException('Invalid monetary amount');
  [$whole,$frac]=array_pad(explode('.',$v,2),2,'');return (int)$whole*100+(int)str_pad($frac,2,'0');
 }
 public static function decimal(int $cents):string {if($cents<0)throw new InvalidArgumentException('Negative cents');return sprintf('%d.%02d',intdiv($cents,100),$cents%100);}
}
