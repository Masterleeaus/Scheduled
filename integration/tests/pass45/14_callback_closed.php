<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->active=true;pass45_throws(fn()=>\App\Booking\Public\Redemption\CommitCallback::call(function()use($p){$p->active=false;return 5;},[],[],$p),'outside transaction');
echo "pass45 14_callback_closed OK\n";
