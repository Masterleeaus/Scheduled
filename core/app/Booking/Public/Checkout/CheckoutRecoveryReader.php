<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;
use App\Booking\Public\Redemption\CommitLegacyProof;
use App\Booking\Public\Redemption\CommitSelection;
/** Read-only exact claim + appointment + order proof on the tenant-selected PDO. */
final class CheckoutRecoveryReader {
 private static function validIdentity(array $identity):bool {
  foreach (['name','email','phone'] as $field) {
   if (!isset($identity[$field]) || !is_string($identity[$field]) || $identity[$field]==='')return false;
  }
  return true;
 }
 public static function read(\PDO $db,string $key,int $owner,string $fingerprint,array $selection,array $expectedIdentity,array $expectedPersonalInfo):?array {
  if(!self::validIdentity($expectedIdentity) || !preg_match('/^[a-f0-9]{64}$/D',$key) || !preg_match('/^[a-f0-9]{64}$/D',$fingerprint) || $owner<1)
   throw new \InvalidArgumentException('Invalid lookup');
  if($db->inTransaction())throw new \RuntimeException('Recovery requires isolated connection');
  $stmt=$db->prepare('SELECT claim_key,owner_id,fingerprint,state,booking_id FROM titan_booking_intent_claims WHERE claim_key=? AND owner_id=? LIMIT 2');
  if($stmt===false || $stmt->execute([$key,$owner])!==true)throw new \RuntimeException('Claim query failed');
  $claims=$stmt->fetchAll(\PDO::FETCH_ASSOC);
  if(!is_array($claims) || count($claims)>1)throw new \RuntimeException('Invalid claim result');
  if(count($claims)===0)return null;
  $claim=$claims[0];
  if(!is_array($claim) || !is_string($claim['fingerprint']??null) || !hash_equals($fingerprint,$claim['fingerprint']))
    throw new \RuntimeException('Claim identity mismatch');
  if(($claim['state']??null)!=='committed')return null;
  $id=filter_var($claim['booking_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
  if($id===false)throw new \RuntimeException('Invalid booking evidence');
  $s=$db->prepare('SELECT id,order_id,order_date,client_id,service_id,method_id,method_unit_id,method_unit_qty,staff_ids,booking_date_time,booking_status FROM ct_bookings WHERE id=? LIMIT 2');
  if($s===false || $s->execute([$id])!==true)throw new \RuntimeException('Booking read failed');
  $rows=$s->fetchAll(\PDO::FETCH_ASSOC);
  if(!is_array($rows)||count($rows)!==1)throw new \RuntimeException('Booking not uniquely persisted');
  $row=$rows[0];
  CommitLegacyProof::verify($row,CommitSelection::parse($selection));
  if((string)($row['id']??'')!==(string)$id)throw new \RuntimeException('Booking reference mismatch');
  // Recovery must corroborate the same Cleanto order_date that the writer verifies.
  // Use the submitted appointment date, not the request's current clock date.
  $expectedDate=substr(CommitSelection::parse($selection)['date_time'],0,10);
  if (!is_string($row['order_date']??null) || $row['order_date']!==$expectedDate)
   throw new \RuntimeException('Persisted order date mismatch');
  $order=filter_var($row['order_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
  if($order===false)throw new \RuntimeException('Missing Cleanto order');
  $o=$db->prepare('SELECT id,order_id,client_name,client_email,client_phone,client_personal_info FROM ct_order_client_info WHERE id=? AND order_id=? LIMIT 2');
  if($o===false || $o->execute([$row['client_id'],$order])!==true)throw new \RuntimeException('Order read failed');
  $orders=$o->fetchAll(\PDO::FETCH_ASSOC);
  if(!is_array($orders)||count($orders)!==1 || (string)($orders[0]['order_id']??'')!==(string)$order)
   throw new \RuntimeException('Order not uniquely persisted');
  // Cleanto uses ct_bookings.client_id to reference ct_order_client_info.id.
  // An order_id match alone is not proof that the recovered booking owns this row.
  if (!ctype_digit((string)($row['client_id']??'')) || (int)$row['client_id']<1 ||
      (string)($orders[0]['id']??'')!==(string)$row['client_id'])
    throw new \RuntimeException('Order customer row linkage mismatch');
  // Check original Cleanto personal-info serialization without unserializing database data.
  if (!is_string($orders[0]['client_personal_info']??null) ||
      !hash_equals(\Titan\BookingSidecar\BookingPersonalInfo::encode($expectedPersonalInfo),$orders[0]['client_personal_info']))
    throw new \RuntimeException('Order service location mismatch');
  // The order reference alone is insufficient: prove that it belongs to the
  // validated customer identity, not an unrelated order linked by mistake.
  foreach (['name'=>'client_name','email'=>'client_email','phone'=>'client_phone'] as $source=>$stored) {
   if (!is_string($orders[0][$stored]??null) ||
       !hash_equals($expectedIdentity[$source],$orders[0][$stored]))
    throw new \RuntimeException('Order customer identity mismatch');
  }
  $payment=CheckoutPaymentRecord::inspect($db,(int)$order);
  return $claim+['booking_verified'=>true,'order_verified'=>true,'booking_status'=>$row['booking_status']??null,'payment_record_status'=>$payment['record_status'],'payment_record_count'=>$payment['record_count']];
 }
}
