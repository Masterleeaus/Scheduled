<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitOrchestrator.php');
if(!(str_contains($src,'RedeemTransaction::run($db'))){fwrite(STDERR,'FAIL commit_in_transaction\n');exit(1);}
echo "PASS commit_in_transaction\n";
