<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Booking\BookingCatalogueGateway;
use App\Booking\BookingTenantRegistry;
use App\Booking\InternalBookingClient;
use App\Services\Sites\SiteBookingIntegration;
use App\Services\Sites\SiteCleantoServiceMapping;
use App\Services\Sites\SiteCleantoServiceReadiness;
use App\Services\Tenants\TenantAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
final class SiteCleantoServiceMapController extends Controller {
 private function site(int $id,int $language):object {
  $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$language)->first();abort_unless($site,404);
  abort_unless((int)($site->tenant_id??0)>0 && SiteBookingIntegration::supported((string)$site->template_key),403);
  TenantAccess::require((int)$site->tenant_id,'owner');
  return $site;
 }
 private function owner(object $site):int {
  $hosts=SiteBookingIntegration::availableHosts($site);
  $owners=array_values(array_unique(array_values($hosts)));
  if(count($owners)!==1)throw ValidationException::withMessages(['services'=>'A verified website and Cleanto booking domain with exactly one owner is required.']);
  return (int)$owners[0];
 }
 public function edit(Request $request,int $id,BookingCatalogueGateway $gateway,BookingTenantRegistry $registry,InternalBookingClient $client){
  $input=$request->validate(['language_id'=>'required|integer|min:1']);$site=$this->site($id,(int)$input['language_id']);
  $owner=$this->owner($site);$catalogue=$gateway->forOwner($owner);
  $selected=SiteCleantoServiceMapping::current($site);
  $valid=SiteCleantoServiceMapping::validForCatalogue($selected,$catalogue);
  $readiness=SiteCleantoServiceReadiness::inspect($catalogue,$selected);
  $managed=[];$serviceManagementAvailable=false;$managedUnits=[];$unitManagementAvailable=false;$staffOverview=[];$staffManagementAvailable=false;
  try{$record=$client->call($registry->forOwner($owner),'owner_service_list',[]);$managed=is_array($record['services']??null)?$record['services']:[];$serviceManagementAvailable=true;}catch(\Throwable $e){$serviceManagementAvailable=false;}
  try{$record=$client->call($registry->forOwner($owner),'owner_service_units',[]);$managedUnits=is_array($record['units']??null)?$record['units']:[];$unitManagementAvailable=true;}catch(\Throwable $e){$unitManagementAvailable=false;}
  try{$record=$client->call($registry->forOwner($owner),'owner_staff_overview',[]);$staffOverview=is_array($record['staff']??null)?$record['staff']:[];$staffManagementAvailable=true;}catch(\Throwable $e){$staffManagementAvailable=false;}
  return view('admin.sites.cleanto-service-map',compact('site','catalogue','selected','valid','input','readiness','managed','serviceManagementAvailable','managedUnits','unitManagementAvailable','staffOverview','staffManagementAvailable'));
 }
 public function update(Request $request,int $id,BookingCatalogueGateway $gateway){
  $input=$request->validate(['language_id'=>'required|integer|min:1','service_ids'=>'required|array|min:1|max:50','service_ids.*'=>'required|integer|min:1']);
  $site=$this->site($id,(int)$input['language_id']);$owner=$this->owner($site);
  $catalogue=$gateway->forOwner($owner);
  try{$ids=SiteCleantoServiceMapping::validateSelection($input['service_ids'],$catalogue);}
  catch(\InvalidArgumentException $e){throw ValidationException::withMessages(['service_ids'=>$e->getMessage()]);}
  SiteCleantoServiceMapping::save($site,$owner,$ids);
  return redirect()->route('admin.sites.booking-services.edit',['id'=>$site->id,'language_id'=>$site->language_id])->with('success','Live Cleanto services mapped to this website.');
 }
}
