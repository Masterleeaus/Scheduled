<?php
namespace App\Booking\Public;
use Illuminate\Support\Facades\DB;
final class PublicDomainRegistry {
 public function ownerForHost(string $host):int {
  $host=PublicHost::normalize($host);
  $rows=DB::table('booking_public_domains')->where('domain',$host)->where('status','active')->limit(2)->get();
  if(count($rows)!==1)throw new \RuntimeException('No unique verified booking domain');
  $binding=PublicDomainBinding::validate($rows[0]);
  if($binding['domain']!==$host)throw new \RuntimeException('Domain mismatch');
  return $binding['owner_user_id'];
 }
}
