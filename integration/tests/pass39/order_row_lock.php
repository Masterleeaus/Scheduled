<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitOrderReader.php');
if(!(str_contains($src,'FOR UPDATE') && str_contains($src,'CommitTransactionBoundary::assert($db)'))){fwrite(STDERR,'FAIL order_row_lock\n');exit(1);}
echo "PASS order_row_lock\n";
