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
/** Owner-only native staff eligibility writes; scheduling is deliberately read-only. */
final class SiteCleantoStaffController extends Controller {
 public function save(Request $request,int $id,BookingTenantRegistry $registry,InternalBookingClient $client){
  $input=$request->validate(['language_id'=>'required|integer|min:1','staff_id'=>'required|integer|min:1','service_id'=>'required|integer|min:1','assigned'=>'required|in:0,1']);
  $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$input['language_id'])->first();
  abort_unless($site && (int)($site->tenant_id??0)>0 && SiteBookingIntegration::supported((string)$site->template_key),404);
  TenantAccess::require((int)$site->tenant_id,'owner');
  $owners=array_values(array_unique(array_values(SiteBookingIntegration::availableHosts($site))));
  if(count($owners)!==1)throw ValidationException::withMessages(['staff'=>'Verify a single matching website and booking owner first.']);
  try{$response=$client->call($registry->forOwner((int)$owners[0]),'owner_staff_service_assignment',['staff_id'=>(int)$input['staff_id'],'service_id'=>(int)$input['service_id'],'assigned'=>$input['assigned']==='1']);}
  catch(\Throwable $e){report($e);throw ValidationException::withMessages(['staff'=>'Cleanto rejected the assignment. Check staff management permissions, service disabled status and tenant configuration.']);}
  if((int)($response['staff_id']??0)!==(int)$input['staff_id']||(int)($response['service_id']??0)!==(int)$input['service_id']||($response['assigned']??null)!==($input['assigned']==='1'))throw ValidationException::withMessages(['staff'=>'Cleanto assignment readback did not match.']);
  return redirect()->route('admin.sites.booking-services.edit',['id'=>$id,'language_id'=>$site->language_id])->with('success','Native disabled-service staff eligibility saved. Working hours were not changed.');
 }
 public function schedule(Request $request,int $id,BookingTenantRegistry $registry,InternalBookingClient $client){
  $input=$request->validate(['language_id'=>'required|integer|min:1','staff_id'=>'required|integer|min:1','shift_id'=>'required|integer|min:1','start'=>'required|date_format:H:i','end'=>'required|date_format:H:i','off'=>'required|in:0,1']);
  $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$input['language_id'])->first();
  abort_unless($site && (int)($site->tenant_id??0)>0 && SiteBookingIntegration::supported((string)$site->template_key),404);
  TenantAccess::require((int)$site->tenant_id,'owner');
  $owners=array_values(array_unique(array_values(SiteBookingIntegration::availableHosts($site))));
  if(count($owners)!==1)throw ValidationException::withMessages(['schedule'=>'Verify one matching website and booking owner first.']);
  $payload=['staff_id'=>(int)$input['staff_id'],'shift_id'=>(int)$input['shift_id'],'start'=>$input['start'].':00','end'=>$input['end'].':00','off'=>$input['off']==='1'];
  try{$result=$client->call($registry->forOwner((int)$owners[0]),'owner_staff_schedule_update',$payload);}
  catch(\Throwable $e){report($e);throw ValidationException::withMessages(['schedule'=>'Cleanto rejected the shift update. This requires a disabled staff account with no booking history, breaks or time off, an existing weekly schedule and tenant schedule-write permission.']);}
  if(($result['verified']??false)!==true || (int)($result['staff_id']??0)!==$payload['staff_id'] || (int)($result['shift_id']??0)!==$payload['shift_id'] || ($result['start']??null)!==$payload['start'] || ($result['end']??null)!==$payload['end'] || ($result['off']??null)!==$payload['off'])throw ValidationException::withMessages(['schedule'=>'Cleanto shift readback did not match the request.']);
  return redirect()->route('admin.sites.booking-services.edit',['id'=>$id,'language_id'=>$site->language_id])->with('success','Disabled staff weekly shift updated and verified in Cleanto.');
 }
}
