<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedLocalClock,PersistedWindowBounds,PersistedInterval,PersistedOverlapQuery,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedEvidenceConsistency,PersistedSingleWinner,PersistedStatusPolicy,PersistedStaffSet,PersistedConcurrencyEvidence};
$w=PersistedFixtureWindow::from(validFixture());$w['start_at']='2027-04-04 02:30:00';expectFailure(fn()=>PersistedWindowBounds::forWindow($w));
echo "PASS pass24_window_dst_fold.php\n";
