<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$s=AcceptanceTestSettings::build(['database'=>'titan_booking_test_alpha'],validFixture(),['BOOKING_LIFECYCLE_ACK'=>LifecycleConsent::TOKEN,'BOOKING_ACCEPT_TEST_ONLY'=>'YES_DISPOSABLE_ONLY']);check($s['acceptance_only_unverified']===true && $s['booking_writes_enabled']===true);
echo "PASS acceptance_test_mode_scoped\n";
