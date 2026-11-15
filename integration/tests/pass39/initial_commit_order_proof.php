<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitOrchestrator.php');
if(!(str_contains($src,'CommitPersistenceProof::assert($db,$id,$selection)') && !str_contains($src,'CommitEvidence::assert(CommitEvidenceReader::read($db,$id),$id)'))){fwrite(STDERR,'FAIL initial_commit_order_proof\n');exit(1);}
echo "PASS initial_commit_order_proof\n";
