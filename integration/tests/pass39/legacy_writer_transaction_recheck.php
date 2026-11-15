<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitLegacyAdapter.php');
if(!(substr_count($src,'CommitTransactionBoundary::assert($db)')===1)){fwrite(STDERR,'FAIL legacy_writer_transaction_recheck\n');exit(1);}
echo "PASS legacy_writer_transaction_recheck\n";
