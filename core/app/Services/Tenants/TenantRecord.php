<?php
namespace App\Services\Tenants;
use Illuminate\Support\Facades\DB;
/** Existing unassigned records remain legacy-admin managed, never silently assigned to a tenant. */
final class TenantRecord {
 public static function requireAssigned(object $record):int {
  $tenant=(int)($record->tenant_id??0);abort_unless($tenant>0,403);TenantAccess::require($tenant);return $tenant;
 }
 public static function scope($query, int $tenantId){TenantAccess::require($tenantId);return $query->where('tenant_id',$tenantId);}
 public static function cloneFields(object $source):array{return ['tenant_id'=>self::requireAssigned($source)];}
}
