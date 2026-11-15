<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
final class SiteLeadValidator {
 public static function rules():array {return ['name'=>'required|string|min:2|max:120','email'=>'required|email|max:160',
  'phone'=>'nullable|string|max:40','service'=>'nullable|string|max:120','message'=>'required|string|min:10|max:3000',
  'utm_source'=>'nullable|string|max:100|regex:/^[A-Za-z0-9_.-]+$/','utm_medium'=>'nullable|string|max:100|regex:/^[A-Za-z0-9_.-]+$/','utm_campaign'=>'nullable|string|max:100|regex:/^[A-Za-z0-9_.-]+$/','promotion_code'=>'nullable|string|max:40','consent'=>'accepted','website'=>'nullable|string|max:100'];}
 public static function check(array $data,array $document): void {
  if(!empty($data['promotion_code'])){
   $codes=array_column(\App\Services\Sites\SiteMarketing::activePromotions($document,date('Y-m-d')),'code');
   if(!in_array($data['promotion_code'],$codes,true))throw ValidationException::withMessages(['site_lead'=>'The selected offer is unavailable.']);
  }
  if(!empty($data['service'])){
   $names=array_column($document['services']??[],'name');
   if(!in_array($data['service'],$names,true))throw ValidationException::withMessages(['site_lead'=>'Please choose a listed service.']);
  }
 }
}
