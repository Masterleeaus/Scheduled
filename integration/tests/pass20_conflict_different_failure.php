<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
expectFailure(fn()=>LifecycleConflictAssertion::check(function(){throw new DomainException('Database disconnected');}));
