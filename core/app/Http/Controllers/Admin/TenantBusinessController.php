<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Tenants\TenantAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
final class TenantBusinessController extends Controller {
 public function index(){return response()->json(['businesses'=>DB::table('titan_business_tenants')->whereIn('id',TenantAccess::ids())->get(['id','name','status'])])->header('Cache-Control','private,no-store');}
 public function store(Request $r){$v=$r->validate(['name'=>'required|string|min:2|max:120']);$admin=TenantAccess::adminId();abort_unless($admin>0,403);
  $id=DB::transaction(function()use($v,$admin){$id=DB::table('titan_business_tenants')->insertGetId(['name'=>$v['name'],'status'=>'active','created_at'=>now(),'updated_at'=>now()]);DB::table('titan_business_memberships')->insert(['tenant_id'=>$id,'admin_id'=>$admin,'role'=>'owner','created_at'=>now(),'updated_at'=>now()]);return $id;});
  return response()->json(['id'=>$id],201);
 }
 public function adopt(Request $r,int $tenantId){TenantAccess::require($tenantId,'owner');$v=$r->validate(['record_type'=>'required|in:site,campaign','record_id'=>'required|integer|min:1']);$table=$v['record_type']==='site'?'titan_sites':'titan_landing_campaigns';
  // Adoption is intentionally disabled until legacy record ownership can be independently verified.
  return response()->json(['error'=>'Legacy ownership verification required before tenant adoption.'],409);
 }
}
