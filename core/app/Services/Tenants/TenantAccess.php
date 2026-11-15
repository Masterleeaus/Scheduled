<?php
namespace App\Services\Tenants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
/** Explicit tenant membership for privileged tenant actions. Language alone is not tenant identity. */
final class TenantAccess {
 public static function adminId():int {return (int)Auth::guard('admin')->id();}
 public static function permitted(int $tenantId,string $role='editor'):bool {
  if($tenantId<1||self::adminId()<1)return false;
  $query=DB::table('titan_business_memberships')->where('tenant_id',$tenantId)->where('admin_id',self::adminId());
  if($role==='owner')$query->where('role','owner');
  return $query->exists()&&DB::table('titan_business_tenants')->where('id',$tenantId)->where('status','active')->exists();
 }
 public static function require(int $tenantId,string $role='editor'):void {abort_unless(self::permitted($tenantId,$role),403);}
 public static function ids():array {
  if(self::adminId()<1)return [];
  return DB::table('titan_business_memberships')->where('admin_id',self::adminId())->pluck('tenant_id')->map(fn($v)=>(int)$v)->all();
 }
}
