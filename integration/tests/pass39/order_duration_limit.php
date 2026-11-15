<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitOrderDuration.php');
if(!(str_contains($src,'$v<15||$v>720'))){fwrite(STDERR,'FAIL order_duration_limit\n');exit(1);}
echo "PASS order_duration_limit\n";
