<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$r=LifecycleReport::from(new LifecycleEvidence());check($r['production_booking_writes_authorized']===false&&$r['full_concurrency_certified']===false);
