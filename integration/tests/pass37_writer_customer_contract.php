<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$s=file_get_contents(__DIR__.'/../../core/app/Booking/Public/Redemption/CommitLegacyAdapter.php');if(!str_contains($s,'CommitCustomerIdentityPolicy::requireSafe'))throw new Exception('unsafe customer');
echo "PASS\n";
