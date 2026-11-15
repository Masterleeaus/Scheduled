<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->active=true;$id=\App\Booking\Public\Redemption\CommitCallback::call(fn()=>5,[],[],$p);pass45_assert($id===5,'id invalid');
echo "pass45 13_callback_id OK\n";
