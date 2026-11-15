<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitReplaySelectionEvidence.php');
if(!(str_contains($src,'CommitBookingStatusPolicy::requireActive'))){fwrite(STDERR,'FAIL replay_status_canonical\n');exit(1);}
echo "PASS replay_status_canonical\n";
