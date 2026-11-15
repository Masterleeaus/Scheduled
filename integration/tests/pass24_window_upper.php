<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedLocalClock,PersistedWindowBounds,PersistedInterval,PersistedOverlapQuery,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedEvidenceConsistency,PersistedSingleWinner,PersistedStatusPolicy,PersistedStaffSet,PersistedConcurrencyEvidence};
$w=PersistedFixtureWindow::from(validFixture());$b=PersistedWindowBounds::forWindow($w);check($b[1]==='2027-01-10 10:00:00');
echo "PASS pass24_window_upper.php\n";
