<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Composite independent acceptance proof; report no customer PII. */
final class LifecycleFullEvidence {
 public static function verify(array $booking,array $order,array $fixture):array {
  $create=$fixture['create']??[];
  LifecycleOwnershipEvidence::verify($booking,$order);
  LifecycleDurationEvidence::verify($order,$create);
  LifecycleServiceEvidence::verify($booking,$create);
  LifecycleCustomerEvidence::verify($order,$create);
  if($booking['booking_status']!=='A')throw new \RuntimeException('Booking not active after creation');
  return ['booking_id'=>$booking['id'],'order_id'=>$booking['order_id'],'linkage_verified'=>true,'duration_verified'=>true,'service_verified'=>true,'customer_verified'=>true,'payment_verified'=>false];
 }
}
