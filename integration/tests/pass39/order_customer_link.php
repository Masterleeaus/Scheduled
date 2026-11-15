<?php
declare(strict_types=1);
$src=file_get_contents(dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitDurationProof.php');
if(!(str_contains($src,'CommitCustomerOrder::validate'))){fwrite(STDERR,'FAIL order_customer_link\n');exit(1);}
echo "PASS order_customer_link\n";
