<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitLegacyFingerprint {
 public static function make(int $owner,array $selection,array $customer,array $personalInfo=[]):string {
  if($owner<1)throw new \InvalidArgumentException('Invalid owner');
  $s=CommitSelection::parse($selection);$c=CommitLegacyCustomer::map($customer);
  ksort($s,SORT_STRING);ksort($c,SORT_STRING);
  // Preserve the historical fingerprint for empty location data. Existing claims
  // must remain replayable; new claims with personal details bind those details.
  $payload=['owner'=>$owner,'selection'=>$s,'customer'=>$c];
  $personal=\Titan\BookingSidecar\BookingPersonalInfo::parse($personalInfo);
  if (array_filter($personal, static fn($value):bool => $value !== '')) {
   ksort($personal,SORT_STRING);
   $payload['personal_info']=$personal;
  }
  return hash('sha256',json_encode($payload,JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES));
 }
}
