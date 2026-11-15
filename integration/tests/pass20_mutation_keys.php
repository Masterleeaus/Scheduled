<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$a=LifecycleMutationKeys::generate();$b=LifecycleMutationKeys::generate();check(LifecycleMutationKeys::distinct($a)&&count($a)===4&&$a!==$b);
