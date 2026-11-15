<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;
use App\Booking\Public\Redemption\CommitLegacyClaimKey;
use App\Booking\Public\Redemption\CommitLegacyFingerprint;
/** Read-only recovery from the original idempotency claim, never a second write. */
final class CheckoutRecovery {
 public static function lookup(int $owner,string $nonce,array $selection,array $customer,callable $reader):array {
  if($owner<1 || !preg_match('/^[a-f0-9]{32,128}$/D',$nonce))throw new \InvalidArgumentException('Invalid recovery identity');
  $mapped=CheckoutNativeCustomer::map($customer);
  CheckoutCommitCompatibility::assert($selection,$customer,$nonce);
  $key=CommitLegacyClaimKey::derive($nonce);
  $hash=CommitLegacyFingerprint::make($owner,$selection,$mapped['identity'],$mapped['personal_info']);
  $proof=$reader($key,$owner,$hash,$selection,$mapped['identity'],$mapped['personal_info']);
  // Lack of a matching committed record means UNKNOWN, not failed or safe to retry.
  if($proof===null)return ['resolved'=>false,'confirmed'=>false,'payment_settled'=>false,'status'=>'unknown'];
  if(!is_array($proof) || ($proof['claim_key']??null)!==$key ||
     (string)($proof['owner_id']??'')!==(string)$owner ||
     !is_string($proof['fingerprint']??null) || !hash_equals($hash,$proof['fingerprint']) ||
     ($proof['state']??null)!=='committed' ||
     !ctype_digit((string)($proof['booking_id']??'')) || (int)$proof['booking_id']<1 ||
     ($proof['booking_verified']??null)!==true || ($proof['order_verified']??null)!==true)
     throw new \RuntimeException('Persisted recovery proof unavailable');
  // A committed claim proves the booking existed, not that it remains confirmed.
  // Cleanto's native statuses: active/confirmed/completed/rescheduled vs cancelled/rejected/no-show.
  $nativeStatus=$proof['booking_status']??null;
  if(!is_string($nativeStatus))throw new \RuntimeException('Booking status evidence unavailable');
  $statuses=['A'=>'confirmed','C'=>'confirmed','CO'=>'completed','RS'=>'rescheduled',
             'CC'=>'cancelled','CS'=>'cancelled','R'=>'rejected','MN'=>'no_show'];
  if(!isset($statuses[$nativeStatus]))throw new \RuntimeException('Unknown booking status');
  $status=$statuses[$nativeStatus];
  return ['resolved'=>true,'confirmed'=>$status==='confirmed','payment_settled'=>false,
          'status'=>$status,'booking_reference'=>(string)$proof['booking_id'],
          'payment_record_status'=>$proof['payment_record_status']??'unknown'];
 }
}
