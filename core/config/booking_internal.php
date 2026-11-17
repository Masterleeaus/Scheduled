<?php
return [
    // Unified production mode rejects all Cleanto HTTP sidecar traffic and MySQL-native checkout.
    // Keep disabled for legacy environments until the new PostgreSQL booking writer is complete.
    'unified_mode' => env('TITAN_UNIFIED_MODE', false),
 'endpoint'=>env('BOOKING_INTERNAL_ENDPOINT',''),
 'secret'=>env('BOOKING_INTERNAL_SECRET',''),
 // Booking writes are tenant-sidecar settings; this Laravel layer cannot enable them.
 'owner_booking_management'=>env('BOOKING_OWNER_MANAGEMENT',true),
 // Reserved, default off, and currently inert: InternalBookingClient refuses booking_create/booking_cancel/booking_reschedule
 // whatever this value is, because BookingOwnerGateway has no route for them. Do not enable without gateway routing.
 'contract_operations_enabled'=>env('BOOKING_CONTRACT_OPERATIONS_ENABLED',false),
 // Owner-initiated booking_create is off unless explicitly enabled in deployment config.
 // The sidecar additionally requires per-tenant verified write flags (BookingWriteFeatureGate).
 'owner_booking_create_enabled'=>env('BOOKING_OWNER_CREATE_ENABLED',false),
    // False until independent real-DB, concurrency and payment checks are passed.
    'public_commit_enabled' => env('TITAN_BOOKING_PUBLIC_COMMIT_ENABLED', false),
    'intent_secret' => env('TITAN_BOOKING_INTENT_SECRET', ''),
    // Deployment-only configuration. Do not populate from public request, and do
    // not enable until independently verified MariaDB, concurrency, pricing and
    // isolation evidence has been recorded for each owner and connection.
    // Format: owner_id => ['connection' => Laravel connection name,
    //   'tenant' => private Cleanto tenant settings,
    //   'release_evidence' => private independently-verified acceptance flags].
    'public_commit_tenants' => [],
    // Opt-in per owner: owner_id => ['enabled'=>true,'connection'=>'cleanto_tenant_x',
    // 'database'=>'exact_registry_database_reference']. Never accept from HTTP input.
    // Requires independently checked MariaDB grants and ownership; disabled by default.
    'native_catalogue_tenants' => [],
];
