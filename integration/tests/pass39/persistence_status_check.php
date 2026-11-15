<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitPersistenceProof.php');
if(!(str_contains($src,'CommitBookingIdentity::validate'))){fwrite(STDERR,'FAIL persistence_status_check\n');exit(1);}
echo "PASS persistence_status_check\n";
