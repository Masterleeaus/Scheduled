<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
final class BookingCancellation {
 public static function parse(array $input):array {
  $id=$input['booking_id']??null;if(!is_int($id)||$id<1)throw new InvalidArgumentException('Invalid booking id');
  $actor=$input['actor']??'';if(!in_array($actor,['customer','business'],true))throw new InvalidArgumentException('Invalid actor');
  $next=$actor==='customer'?'CC':'CS';
  $reason=trim((string)($input['reason']??''));if(strlen($reason)>200)throw new InvalidArgumentException('Reason exceeds legacy field');
  return ['booking_id'=>$id,'next_status'=>$next,'reason'=>$reason,'idempotency_key'=>BookingKey::normalize($input['idempotency_key']??null)];
 }
}
