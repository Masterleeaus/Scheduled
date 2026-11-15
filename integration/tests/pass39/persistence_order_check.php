<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitPersistenceProof.php');
if(!(str_contains($src,'CommitOrderReader::read') && str_contains($src,'CommitDurationProof::validate'))){fwrite(STDERR,'FAIL persistence_order_check\n');exit(1);}
echo "PASS persistence_order_check\n";
