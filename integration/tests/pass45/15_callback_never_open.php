<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;pass45_throws(fn()=>\App\Booking\Public\Redemption\CommitCallback::call(fn()=>9,[],[],$p),'outside transaction');
echo "pass45 15_callback_never_open OK\n";
