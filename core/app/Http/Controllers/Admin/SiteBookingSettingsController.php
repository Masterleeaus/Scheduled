<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Sites\SiteBookingIntegration;
use App\Services\Tenants\TenantAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
final class SiteBookingSettingsController extends Controller {
 public function update(Request $request,int $id){
  $input=$request->validate(['language_id'=>'required|integer|min:1','enabled'=>'required|in:0,1','hostname'=>'nullable|string|max:253']);
  $site=DB::table('titan_sites')->where('id',$id)->where('language_id',(int)$input['language_id'])->first();abort_unless($site,404);
  abort_unless((int)($site->tenant_id??0)>0 && SiteBookingIntegration::supported((string)$site->template_key),403);
  TenantAccess::require((int)$site->tenant_id,'owner');
  if($input['enabled']==='1'){
    $host=(string)($input['hostname']??'');
   $allowed=SiteBookingIntegration::availableHosts($site);
   $owner=$allowed[$host]??null;
   if($owner===null)throw ValidationException::withMessages(['enabled'=>'Enable availability only on the verified custom domain that is also registered to an active Cleanto booking instance.']);
   DB::table('titan_site_booking_settings')->updateOrInsert(['site_id'=>$site->id],[
    'tenant_id'=>$site->tenant_id,'owner_user_id'=>$owner,'hostname'=>$host,'enabled'=>true,'updated_at'=>now()]);
  }else{
   DB::table('titan_site_booking_settings')->where('site_id',$site->id)->where('tenant_id',$site->tenant_id)->update(['enabled'=>false,'updated_at'=>now()]);
  }
  return redirect()->route('admin.sites.edit',['id'=>$site->id,'language_id'=>$site->language_id])->with('success','Booking availability setting saved.');
 }
}
