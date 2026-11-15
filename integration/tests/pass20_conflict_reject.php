<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
LifecycleConflictAssertion::check(function(){throw new DomainException('Staff already booked');});
