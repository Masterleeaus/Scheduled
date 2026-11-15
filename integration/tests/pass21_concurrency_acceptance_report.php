<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$r=ConcurrencyAcceptanceReport::make(['passed'=>true,'accepted_booking_id'=>10,'other_rejected'=>true],'titan_booking_test_alpha');check($r['production_booking_writes_authorized']===false && $r['actual_two_process_attempts']===true);
echo "PASS concurrency_acceptance_report\n";
