<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Tenants\{TenantAccess,DomainHostname,DomainVerification};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
final class TenantDomainController extends Controller {
 private function target(int $tenantId,string $type,int $targetId):void {
  TenantAccess::require($tenantId,'owner');abort_unless(in_array($type,['site','campaign'],true),404);
  $table=$type==='site'?'titan_sites':'titan_landing_campaigns';
  abort_unless(DB::table($table)->where('id',$targetId)->where('tenant_id',$tenantId)->exists(),404);
 }
 public function index(Request $r,int $tenantId){TenantAccess::require($tenantId);$domains=DB::table('titan_site_domains')->where('tenant_id',$tenantId)->orderBy('hostname')->get();return response()->json(['domains'=>$domains->map(fn($d)=>['id'=>$d->id,'hostname'=>$d->hostname,'target_type'=>$d->target_type,'target_id'=>$d->target_id,'verified'=>(bool)$d->verified_at,'active'=>(bool)$d->activated_at,'txt_name'=>DomainVerification::txtName($d->hostname),'txt_value'=>'titan-site='.$d->verification_token])])->header('Cache-Control','private,no-store');}
 public function create(Request $r,int $tenantId){$v=$r->validate(['hostname'=>'required|string|max:253','target_type'=>'required|in:site,campaign','target_id'=>'required|integer|min:1']);$this->target($tenantId,$v['target_type'],(int)$v['target_id']);
  try{$host=DomainHostname::normalize($v['hostname']);}catch(\InvalidArgumentException $e){throw ValidationException::withMessages(['hostname'=>'Enter a valid hostname without scheme or port.']);}
  if(DB::table('titan_site_domains')->where('hostname',$host)->exists())throw ValidationException::withMessages(['hostname'=>'Domain already assigned.']);
  try{$id=DB::table('titan_site_domains')->insertGetId(['tenant_id'=>$tenantId,'hostname'=>$host,'target_type'=>$v['target_type'],'target_id'=>$v['target_id'],'verification_token'=>bin2hex(random_bytes(24)),'created_at'=>now(),'updated_at'=>now()]);}catch(\Illuminate\Database\QueryException $e){throw ValidationException::withMessages(['hostname'=>'Unable to claim domain.']);}
  return response()->json(['id'=>$id,'verification'=>'pending'],201)->header('Cache-Control','private,no-store');
 }
 public function verify(Request $r,int $tenantId,int $id){TenantAccess::require($tenantId,'owner');$d=DB::table('titan_site_domains')->where('tenant_id',$tenantId)->where('id',$id)->first();abort_unless($d,404);
  abort_unless(DomainVerification::verify($d->hostname,$d->verification_token),422,'DNS TXT verification has not succeeded.');
  DB::table('titan_site_domains')->where('tenant_id',$tenantId)->where('id',$id)->update(['verified_at'=>now(),'updated_at'=>now()]);return response()->json(['verified'=>true]);
 }
 public function activate(Request $r,int $tenantId,int $id){TenantAccess::require($tenantId,'owner');$d=DB::table('titan_site_domains')->where('tenant_id',$tenantId)->where('id',$id)->first();abort_unless($d&&$d->verified_at,422,'Verify domain first.');
  // DNS proof is repeated at activation; reverse proxy / TLS readiness is an external deployment gate.
  abort_unless(DomainVerification::verify($d->hostname,$d->verification_token),422,'Domain proof expired.');
  DB::table('titan_site_domains')->where('tenant_id',$tenantId)->where('id',$id)->update(['activated_at'=>now(),'updated_at'=>now()]);return response()->json(['active'=>true]);
 }
 public function deactivate(Request $r,int $tenantId,int $id){TenantAccess::require($tenantId,'owner');$affected=DB::table('titan_site_domains')->where('tenant_id',$tenantId)->where('id',$id)->update(['activated_at'=>null,'updated_at'=>now()]);abort_unless($affected,404);return response()->json(['active'=>false]);}
}
