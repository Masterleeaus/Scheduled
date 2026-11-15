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

/** Optional native unit metadata editing; owner and domain verification required. */
final class SiteCleantoServiceUnitController extends Controller {
 public function save(Request $request,int $id,BookingTenantRegistry $registry,InternalBookingClient $client){
  $v=$request->validate(['language_id'=>'required|integer|min:1','unit_id'=>'required|integer|min:1','base_price'=>['required','regex:/^(?:0|[1-9][0-9]{0,7})(?:\\.[0-9]{1,2})?$/D'],'duration_minutes'=>'required|integer|min:5|max:1440|multiple_of:5']);
  $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$v['language_id'])->first();
  abort_unless($site && (int)($site->tenant_id??0)>0 && SiteBookingIntegration::supported((string)$site->template_key),404);
  TenantAccess::require((int)$site->tenant_id,'owner');
  $owners=array_values(array_unique(array_values(SiteBookingIntegration::availableHosts($site))));
  if(count($owners)!==1)throw ValidationException::withMessages(['unit'=>'A verified website and booking domain with exactly one owner is required.']);
  try{$result=$client->call($registry->forOwner((int)$owners[0]),'owner_service_unit_update',['unit_id'=>(int)$v['unit_id'],'base_price'=>(string)$v['base_price'],'duration_minutes'=>(int)$v['duration_minutes']]);}
  catch(\Throwable $e){report($e);throw ValidationException::withMessages(['unit'=>'Cleanto rejected the update. Only unused units of disabled services may be edited when tenant unit management is enabled.']);}
  if((int)($result['unit_id']??0)!==(int)$v['unit_id']||(int)($result['duration_minutes']??0)!==(int)$v['duration_minutes'])throw ValidationException::withMessages(['unit'=>'Cleanto did not confirm the saved unit.']);
  return redirect()->route('admin.sites.booking-services.edit',['id'=>$id,'language_id'=>$site->language_id])->with('success','Disabled service unit base price and duration saved in Cleanto. Booking activation still requires method, rate, staff and schedule verification.');
 }
}
