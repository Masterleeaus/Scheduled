<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedLocalClock,PersistedWindowBounds,PersistedInterval,PersistedOverlapQuery,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedEvidenceConsistency,PersistedSingleWinner,PersistedStatusPolicy,PersistedStaffSet,PersistedConcurrencyEvidence};
$a=PersistedInterval::fromLocal('2027-01-10 09:00:00',60,'Australia/Melbourne');$b=PersistedInterval::fromLocal('2027-01-10 09:30:00',60,'Australia/Melbourne');check(PersistedInterval::overlap($a,$b));
echo "PASS pass24_ordinary_interval_overlap.php\n";
