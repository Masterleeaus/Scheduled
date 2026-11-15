<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$o=ConcurrencyOutcome::assertSingleWinner(ConcurrencyResult::accepted(3),ConcurrencyResult::rejected(new DomainException('Already booked')));check($o['passed']===true && $o['accepted_booking_id']===3);
echo "PASS concurrency_outcome_success\n";
