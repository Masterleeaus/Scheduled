<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
expectFailure(fn()=>AcceptanceTestSettings::build(['database'=>'titan_booking_test_alpha'],validFixture(),['BOOKING_LIFECYCLE_ACK'=>LifecycleConsent::TOKEN]));
echo "PASS acceptance_requires_explicit_test_mode\n";
