<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$e=new LifecycleEvidence();expectFailure(fn()=>$e->mark('pretend_production_ready'));
