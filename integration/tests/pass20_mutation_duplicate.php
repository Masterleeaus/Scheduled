<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
check(!LifecycleMutationKeys::distinct(['a'=>'same','b'=>'same']));
