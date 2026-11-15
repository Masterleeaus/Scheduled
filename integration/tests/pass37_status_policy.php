<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$p=App\Booking\Public\Redemption\CommitBookingStatusPolicy::class;foreach(['A','C','CO','RS'] as $s)if(!$p::active($s))throw new Exception('active missing');foreach(['X','CAN',null,true,0] as $s)if($p::active($s))throw new Exception('inactive accepted');
echo "PASS\n";
