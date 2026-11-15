<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitOrchestrator.php');
if(!(str_contains($src,'CommitReplayOrderProof::assert($db,$id,$selection)'))){fwrite(STDERR,'FAIL replay_order_proof\n');exit(1);}
echo "PASS replay_order_proof\n";
