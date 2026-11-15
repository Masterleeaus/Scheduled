<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$p=App\Booking\Public\Redemption\CommitCustomerIdentityPolicy::class;$x=$p::requireSafe(['name'=>' Jane ','email'=>'JANE@example.org','phone'=>'+61 400 000 000']);if($x['email']!=='jane@example.org'||$x['name']!=='Jane')throw new Exception('normalization');
echo "PASS\n";
