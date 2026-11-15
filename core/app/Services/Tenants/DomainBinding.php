<?php
namespace App\Services\Tenants;
use Illuminate\Support\Facades\DB;
final class DomainBinding {
 public static function assertPublicHost(?int $tenantId,string $target,int $recordId,string $requestHost):void {
  // Legacy sites have no custom-domain binding. They continue on the legacy canonical route.
  if(!$tenantId)return;
  $bindings=DB::table('titan_site_domains')->where('tenant_id',$tenantId)->where('target_type',$target)->where('target_id',$recordId)->whereNotNull('activated_at');
  if(!$bindings->exists())return;
  $host=DomainHostname::normalize($requestHost);
  abort_unless((clone $bindings)->where('hostname',$host)->whereNotNull('verified_at')->exists(),404);
 }
 public static function available(int $tenantId,string $target,int $recordId):array {
  TenantAccess::require($tenantId);
  return DB::table('titan_site_domains')->where('tenant_id',$tenantId)->where('target_type',$target)->where('target_id',$recordId)->orderBy('hostname')->get()->toArray();
 }
}
