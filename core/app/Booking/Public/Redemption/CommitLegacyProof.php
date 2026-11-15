<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Verify the selected service and staff from persisted Cleanto row, not callback return. */
final class CommitLegacyProof {
 public static function verify(array $row,array $s):void {
  foreach(['service_id'=>'service_id','method_id'=>'method_id','method_unit_id'=>'unit_id','method_unit_qty'=>'quantity'] as $db=>$field){
   $value=$row[$db]??null;
   if(!(is_int($value)||(is_string($value)&&preg_match('/^[1-9][0-9]*$/D',$value)))||(int)$value!==$s[$field])throw new \RuntimeException('Persisted service mismatch');
  }
  $staff=$row['staff_ids']??null;
  if(!is_string($staff)||!preg_match('/^[1-9][0-9]*$/D',$staff)||(int)$staff!==$s['staff_id'])throw new \RuntimeException('Persisted staff mismatch');
  if(($row['booking_date_time']??null)!==$s['date_time'])throw new \RuntimeException('Persisted appointment time mismatch');
 }
}
