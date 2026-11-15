<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Explicit wire format; never trust unbounded worker output as evidence. */
final class ConcurrencyResult {
 public static function accepted(int $id):array {if($id<1||$id>2147483647)throw new \InvalidArgumentException('Booking id invalid');return ['outcome'=>'accepted','booking_id'=>$id];}
 public static function rejected(\Throwable $e):array {return ['outcome'=>'rejected','reason'=>substr(LifecycleFailureRedactor::message($e),0,1024)];}
 public static function assertShape(array $r):void {
  if(($r['outcome']??null)==='accepted' && count($r)===2 && is_int($r['booking_id']??null) && $r['booking_id']>0 && $r['booking_id']<=2147483647)return;
  if(($r['outcome']??null)==='rejected' && count($r)===2 && is_string($r['reason']??null) && strlen($r['reason'])>0 && strlen($r['reason'])<=1024)return;
  throw new \DomainException('Malformed worker result');
 }
}
