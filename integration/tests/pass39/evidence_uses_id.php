<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitEvidence.php');
if(!(str_contains($src,chr(36)."row['id']??null") && !str_contains($src,chr(36)."row['booking_id']??null"))){fwrite(STDERR,'FAIL evidence_uses_id\n');exit(1);}
echo "PASS evidence_uses_id\n";
