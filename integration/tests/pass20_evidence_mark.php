<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$e=new LifecycleEvidence();$e->mark('real_insert');check($e->export()['real_insert']===true&&$e->export()['concurrent_overlap']===false);
