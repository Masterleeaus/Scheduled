<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingPriceInput {
 public static function parse(array $r):array {
  $ids=[];foreach(['service_id','method_id','method_unit_id','quantity'] as $field){$n=$r[$field]??null;if(!is_int($n)||$n<1||$n>10000)throw new \InvalidArgumentException('Invalid '.$field);$ids[$field]=$n;}return $ids;
 }
}
