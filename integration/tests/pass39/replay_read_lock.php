<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitReplayPersistedReader.php');
if(!(str_contains($src,'CommitTransactionBoundary::assert($db)'))){fwrite(STDERR,'FAIL replay_read_lock\n');exit(1);}
echo "PASS replay_read_lock\n";
