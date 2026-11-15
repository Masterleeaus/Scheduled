<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$p=App\Booking\Public\Redemption\CommitResultPolicy::class;try{$p::confirmed(['confirmed'=>true,'booking_id'=>9,'payment_settled'=>true]);throw new Exception('accepted');}catch(RuntimeException $e){if($e->getMessage()==='accepted')throw $e;}
echo "PASS\n";
