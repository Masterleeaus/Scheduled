<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedLocalClock,PersistedWindowBounds,PersistedInterval,PersistedOverlapQuery,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedEvidenceConsistency,PersistedSingleWinner,PersistedStatusPolicy,PersistedStaffSet,PersistedConcurrencyEvidence};
$w=PersistedFixtureWindow::from(validFixture());$b=PersistedWindowBounds::forWindow($w);check(strtotime($w['start_at'].' Australia/Melbourne')-strtotime($b[0].' Australia/Melbourne')>=43200*60);
echo "PASS pass24_window_lookback.php\n";
