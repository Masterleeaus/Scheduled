<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitEvidenceReader.php');
if(!(str_contains($src,'count($rows)!==1'))){fwrite(STDERR,'FAIL evidence_row_unique\n');exit(1);}
echo "PASS evidence_row_unique\n";
