<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitOrchestrator.php');
if(!(strpos($src,'CommitPersistenceProof::assert($db,$id,$selection)') < strpos($src,'$ledger->commit($key,$owner,$fingerprint,$id)'))){fwrite(STDERR,'FAIL initial_commit_before_ledger\n');exit(1);}
echo "PASS initial_commit_before_ledger\n";
