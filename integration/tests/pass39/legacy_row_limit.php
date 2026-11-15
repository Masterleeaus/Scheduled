<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitLegacyEvidenceReader.php');
if(!(str_contains($src,'LIMIT 2 FOR UPDATE') && str_contains($src,'count($rows)!==1'))){fwrite(STDERR,'FAIL legacy_row_limit\n');exit(1);}
echo "PASS legacy_row_limit\n";
