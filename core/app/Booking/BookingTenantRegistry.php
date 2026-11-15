<?php

namespace App\Booking;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Tenant ownership must come from authenticated server context, not request input. */
final class BookingTenantRegistry
{
    public function forOwner(int $authenticatedOwnerId): array
    {
        if ($authenticatedOwnerId <= 0) {
            throw new RuntimeException('Authenticated owner required.');
        }
        $row = DB::table('booking_tenant_instances')
            ->where('owner_user_id', $authenticatedOwnerId)
            ->where('status', 'active')
            ->first();
        if (!$row) {
            throw new RuntimeException('Active booking instance not configured.');
        }
        return [
            'id' => (int) $row->id,
            'owner_user_id' => (int) $row->owner_user_id,
            'instance_key' => (string) $row->instance_key,
            'database_reference' => (string) $row->database_reference,
        ];
    }

    public function cleantoServiceId(int $instanceId, int $plusagencyServiceId): int
    {
        $result = DB::table('booking_service_links')
            ->where('booking_tenant_instance_id', $instanceId)
            ->where('plusagency_service_id', $plusagencyServiceId)
            ->value('cleanto_service_id');
        if (!$result) {
            throw new RuntimeException('Service is not linked for this tenant.');
        }
        return (int) $result;
    }
}
