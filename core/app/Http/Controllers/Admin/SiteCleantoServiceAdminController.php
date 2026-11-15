<?php
declare(strict_types=1);
namespace App\Http\Controllers\Admin;
use App\Booking\BookingTenantRegistry;
use App\Booking\InternalBookingClient;
use App\Http\Controllers\Controller;
use App\Services\Sites\SiteBookingIntegration;
use App\Services\Tenants\TenantAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Authenticated tenant-owner only. The signed sidecar owns every native Cleanto write. */
final class SiteCleantoServiceAdminController extends Controller {
 public function save(Request $request,int $id,BookingTenantRegistry $registry,InternalBookingClient $client){
  $input=$request->validate([
   'language_id'=>'required|integer|min:1',
   'service_id'=>'nullable|integer|min:1',
   'title'=>'required|string|max:120',
   'description'=>'nullable|string|max:2000',
  ]);
  $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$input['language_id'])->first();
  abort_unless($site && (int)($site->tenant_id??0)>0 && SiteBookingIntegration::supported((string)$site->template_key),404);
  TenantAccess::require((int)$site->tenant_id,'owner');
  $hosts=SiteBookingIntegration::availableHosts($site);$owners=array_values(array_unique(array_values($hosts)));
  if(count($owners)!==1)throw ValidationException::withMessages(['service'=>'Verify exactly one website/booking domain and owner first.']);
  $tenant=$registry->forOwner((int)$owners[0]);
  $creating=empty($input['service_id']);
  $payload=['title'=>trim($input['title']),'description'=>trim((string)($input['description']??''))];
  if(!$creating)$payload['id']=(int)$input['service_id'];
  try{$result=$client->call($tenant,$creating?'owner_service_create':'owner_service_update',$payload);}
  catch(\Throwable $e){report($e);throw ValidationException::withMessages(['service'=>'Native Cleanto service update was not accepted. Check tenant service-management permissions and database configuration.']);}
  if(!isset($result['id'])||(int)$result['id']<1)throw ValidationException::withMessages(['service'=>'Cleanto did not confirm the service record.']);
  return redirect()->route('admin.sites.booking-services.edit',['id'=>$id,'language_id'=>$site->language_id])->with('success',$creating?'Service created disabled in Cleanto. Configure its pricing, units, staff and schedule before activation.':'Cleanto service details saved.');
 }
}
