<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedLocalClock,PersistedWindowBounds,PersistedInterval,PersistedOverlapQuery,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedEvidenceConsistency,PersistedSingleWinner,PersistedStatusPolicy,PersistedStaffSet,PersistedConcurrencyEvidence};
$w=PersistedFixtureWindow::from(validFixture());$w['interval'][0]++;expectFailure(fn()=>PersistedWindowBounds::forWindow($w));
echo "PASS pass24_window_tampered.php\n";
