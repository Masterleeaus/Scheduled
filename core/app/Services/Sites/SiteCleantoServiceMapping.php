<?php
declare(strict_types=1);
namespace App\Services\Sites;
use App\Booking\BookingCatalogueGateway;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
/** Business-owner approved catalogue IDs. Display filter, not an authorisation boundary for the Cleanto API. */
final class SiteCleantoServiceMapping {
 public static function validateSelection(array $selected,array $catalogue):array {
  $available=[];
  foreach(($catalogue['services']??[]) as $row){if(isset($row['id'])&&filter_var($row['id'],FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]))$available[(int)$row['id']]=true;}
  if(!array_is_list($selected)||count($selected)>50)throw new InvalidArgumentException('Invalid service selection');
  $ids=[];
  foreach($selected as $id){
   if(!is_scalar($id)||!preg_match('/^[1-9][0-9]*$/D',(string)$id)||!isset($available[(int)$id]))throw new InvalidArgumentException('A selected service is not in the verified Cleanto catalogue');
   $ids[]=(int)$id;
  }
  $ids=array_values(array_unique($ids));sort($ids,SORT_NUMERIC);
  if(count($ids)===0)throw new InvalidArgumentException('Select at least one Cleanto service');
  return $ids;
 }
 public static function current(object $site):array {
  $raw=DB::table('titan_site_booking_service_maps')->where('site_id',(int)$site->id)->where('tenant_id',(int)$site->tenant_id)->first();
  if(!$raw)return [];
  $parsed=json_decode((string)$raw->service_ids,true);
  return is_array($parsed)&&array_is_list($parsed)?array_values(array_filter($parsed,fn($v)=>is_int($v)&&$v>0)):[];
 }
 public static function validForCatalogue(array $selected,array $catalogue):array {
  if(!$selected)return [];
  try{return self::validateSelection($selected,$catalogue);}catch(InvalidArgumentException $e){return [];}
 }
 public static function save(object $site,int $owner,array $ids):void {
  DB::table('titan_site_booking_service_maps')->updateOrInsert(['site_id'=>(int)$site->id],[
   'tenant_id'=>(int)$site->tenant_id,'owner_user_id'=>$owner,'service_ids'=>json_encode($ids,JSON_THROW_ON_ERROR),'updated_at'=>now()]);
 }
}
