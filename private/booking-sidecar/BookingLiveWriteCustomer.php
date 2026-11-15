<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Database-specific limits are checked even if an upstream mapper changes. */
final class BookingLiveWriteCustomer {
 public static function assert(array $customer):void {
  foreach(['name'=>200,'email'=>200,'phone'=>15] as $field=>$max){$v=$customer[$field]??null;if(!is_string($v)||trim($v)===''||strlen($v)>$max)throw new \InvalidArgumentException('Customer '.$field.' does not fit Cleanto column');}
 }
}
