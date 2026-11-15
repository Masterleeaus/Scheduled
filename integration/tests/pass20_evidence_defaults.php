<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$e=new LifecycleEvidence();check(!$e->productionCertified()&&!in_array(true,$e->export(),true));
