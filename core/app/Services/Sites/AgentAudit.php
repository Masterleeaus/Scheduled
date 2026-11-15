<?php
namespace App\Services\Sites;
use App\Services\Tenants\TenantAccess;
use Illuminate\Support\Facades\DB;
/** Append-only audit for human decisions and proposed AI mutations. */
final class AgentAudit {
 public static function write(int $siteId,int $proposalId,string $action,string $detail,int $revision,?int $tenantId): void {
  DB::table('titan_agent_audit_events')->insert([
   'site_id'=>$siteId,'tenant_id'=>$tenantId,'proposal_id'=>$proposalId,
   'admin_id'=>TenantAccess::adminId(),'action'=>$action,'detail'=>mb_substr($detail,0,80),
   'source_revision'=>$revision,'created_at'=>now()
  ]);
 }
}
