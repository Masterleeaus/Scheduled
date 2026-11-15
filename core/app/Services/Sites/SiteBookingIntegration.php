<?php
declare(strict_types=1);
namespace App\Services\Sites;
use Illuminate\Support\Facades\DB;
use App\Booking\Public\PublicHost;
/** One website can use only the booking owner independently verified for its own activated hostname.
 *  This bridge NEVER enables reservation writes. Booking acceptance stays with Cleanto release gates.
 */
final class SiteBookingIntegration {
 public const THEMES=['pawfy','stevenwatkins','yoga-pilates','carzone'];
 public static function supported(string $template):bool {return in_array($template,self::THEMES,true);}
 public static function labels(string $template, ?string $verticalKey = null):array {
  $presentation=BookingVerticalPresentation::forVertical($verticalKey,$template);
  return ['title'=>$presentation['title'],'service'=>$presentation['service'],'staff'=>$presentation['staff'],'size'=>$presentation['size'],'units'=>$presentation['units'],'duration'=>$presentation['duration'],'address'=>$presentation['address'],'staff_prompt'=>'Choose '.strtolower($presentation['staff']),'pricing'=>$presentation['pricing'],'schedule'=>$presentation['schedule']];
 }
 private static function legacyLabels(string $template):array {
  return match($template){
   'pawfy'=>['title'=>'Check pet-care availability','service'=>'Pet-care service','staff'=>'Pet-care professional','size'=>'Service option','units'=>'Number of pets','duration'=>'Appointment duration','address'=>'Service location / address','staff_prompt'=>'Choose a pet-care professional'],
   'stevenwatkins'=>['title'=>'Check training availability','service'=>'Training session','staff'=>'Personal trainer','size'=>'Session option','units'=>'Number of participants','duration'=>'Session duration','address'=>'Training location / address','staff_prompt'=>'Choose a trainer'],
   'yoga-pilates'=>['title'=>'Check yoga and Pilates availability','service'=>'Yoga or Pilates session','staff'=>'Instructor','size'=>'Class option','units'=>'Number of participants','duration'=>'Class duration','address'=>'Session location / address','staff_prompt'=>'Choose an instructor'],
   'carzone'=>['title'=>'Check vehicle-care availability','service'=>'Vehicle-care service','staff'=>'Detailing professional','size'=>'Service option','units'=>'Number of vehicles','duration'=>'Service duration','address'=>'Vehicle location / address','staff_prompt'=>'Choose a detailing professional'],
   default=>throw new \InvalidArgumentException('Unsupported theme')};
 }
 /** Returns null unless the site, website domain and booking domain all agree. */
 public static function verifiedOwner(object $site,string $host):?int {
  if(!self::supported((string)$site->template_key)||(int)($site->tenant_id??0)<1 || (int)($site->id??0)<1)return null;
  try {$host=PublicHost::normalize($host);}catch(\Throwable $e){return null;}
  $domain=DB::table('titan_site_domains')->where('hostname',$host)->where('tenant_id',(int)$site->tenant_id)
   ->where('target_type','site')->where('target_id',(int)$site->id)
   ->whereNotNull('activated_at')->whereNotNull('verified_at')->limit(2)->get();
  if(count($domain)!==1)return null;
  $bookingDomain=DB::table('booking_public_domains')->where('domain',$host)
   ->where('status','active')->whereNotNull('verified_at')->limit(2)->get();
  if(count($bookingDomain)!==1)return null;
  $owner=(int)$bookingDomain[0]->owner_user_id;
  if($owner<1 || !DB::table('booking_tenant_instances')->where('owner_user_id',$owner)->where('status','active')->exists())return null;
  return $owner;
 }
 /** Candidate domains are derived from the site's own verified domain records and Cleanto verification. */
 public static function availableHosts(object $site):array {
  if(!self::supported((string)$site->template_key)||(int)($site->tenant_id??0)<1)return [];
  $rows=DB::table('titan_site_domains')->where('tenant_id',(int)$site->tenant_id)
   ->where('target_type','site')->where('target_id',(int)$site->id)
   ->whereNotNull('verified_at')->whereNotNull('activated_at')->get();
  $available=[];
  foreach($rows as $row){
   $host=(string)$row->hostname;
   $owner=self::verifiedOwner($site,$host);
   if($owner!==null)$available[$host]=$owner;
  }
  return $available;
 }
 public static function active(object $site,string $host,bool $preview=false):bool {
  if($preview||!self::supported((string)$site->template_key))return false;
  try {$host=PublicHost::normalize($host);}catch(\Throwable $e){return false;}
  $owner=self::verifiedOwner($site,$host);if($owner===null)return false;
  return DB::table('titan_site_booking_settings')->where('site_id',(int)$site->id)
   ->where('tenant_id',(int)$site->tenant_id)->where('owner_user_id',$owner)
   ->where('hostname',$host)->where('enabled',true)->exists();
 }
}
