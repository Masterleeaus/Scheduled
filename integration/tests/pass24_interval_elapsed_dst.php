<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedLocalClock,PersistedWindowBounds,PersistedInterval,PersistedOverlapQuery,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedEvidenceConsistency,PersistedSingleWinner,PersistedStatusPolicy,PersistedStaffSet,PersistedConcurrencyEvidence};
$r=PersistedInterval::fromLocal('2027-10-03 01:30:00',120,'Australia/Melbourne');check($r[1]-$r[0]===7200);
echo "PASS pass24_interval_elapsed_dst.php\n";
