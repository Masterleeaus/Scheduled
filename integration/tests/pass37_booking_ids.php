<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$p=App\Booking\Public\Redemption\CommitBookingOrderPolicy::class;$p::requireIds(['id'=>1,'client_id'=>'2','order_id'=>3]);try{$p::requireIds(['id'=>1,'client_id'=>0,'order_id'=>3]);throw new Exception('accepted');}catch(InvalidArgumentException $e){if($e->getMessage()==='accepted')throw $e;}
echo "PASS\n";
