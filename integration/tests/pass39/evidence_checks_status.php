<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitEvidence.php');
if(!(str_contains($src,'CommitBookingStatusPolicy::requireActive'))){fwrite(STDERR,'FAIL evidence_checks_status\n');exit(1);}
echo "PASS evidence_checks_status\n";
