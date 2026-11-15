<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyOutcome {
 public static function assertSingleWinner(array $left,array $right):array {
  ConcurrencyResult::assertShape($left);ConcurrencyResult::assertShape($right);
  $accepted=array_values(array_filter([$left,$right],fn($r)=>$r['outcome']==='accepted'));
  $rejected=array_values(array_filter([$left,$right],fn($r)=>$r['outcome']==='rejected'));
  if(count($accepted)!==1||count($rejected)!==1)throw new \RuntimeException('Concurrent attempts did not produce exactly one winner');
  ConcurrencyResultGuard::assertDistinct($left,$right);
  if(!ConcurrencyFailureCategory::isConflict($rejected[0]['reason']))throw new \RuntimeException('Rejected attempt failed for unrelated reason');
  return ['passed'=>true,'accepted_booking_id'=>$accepted[0]['booking_id'],'other_rejected'=>true];
 }
}
