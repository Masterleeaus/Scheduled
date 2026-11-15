<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
expectFailure(fn()=>AcceptanceTestSettings::build(['database'=>'titan_booking_test_other'],validFixture(),['BOOKING_LIFECYCLE_ACK'=>LifecycleConsent::TOKEN,'BOOKING_ACCEPT_TEST_ONLY'=>'YES_DISPOSABLE_ONLY']));
echo "PASS acceptance_wrong_database\n";
