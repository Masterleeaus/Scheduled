<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Booking persistence never demonstrates payment settlement. Replay must not initiate a second charge. */
final class BookingWriteResponseGuard {
 public static function sanitize(array $response):array {
  $id=$response['booking_id']??null;
  if(!is_int($id)||$id<1||$id>2147483647)throw new \DomainException('Booking was not persisted');
  $replayed=$response['replayed']??null;
  if(!is_bool($replayed))throw new \DomainException('Booking replay state missing');
  $order=$response['order_id']??null;
  if(!is_int($order)||$order<1||$order>2147483647)throw new \DomainException('Booking order was not persisted');
  // A verified replay proves the same reservation, not that it remains unpaid.
  // Keep all payment attempts disabled until a separate authoritative payment lookup.
  if($replayed){
   $response['payment_status']='unknown';
   $response['requires_payment_processing']=false;
   $response['payment_reconciliation_required']=true;
  }else{
   $response['payment_status']='unpaid';
   $response['requires_payment_processing']=true;
   $response['payment_reconciliation_required']=false;
  }
  return $response;
 }
}
