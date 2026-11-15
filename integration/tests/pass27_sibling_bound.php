<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$s=file_get_contents(__DIR__.'/../mysql/lifecycle/LifecycleSiblingReader.php');if(!str_contains($s,'LIMIT 501')||!str_contains($s,'count($rows)>500'))throw new Exception('no bound');
echo "PASS pass27_sibling_bound\n";
