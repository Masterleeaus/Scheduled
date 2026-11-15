<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
$src=file_get_contents($root.'/core/app/Booking/Public/Redemption/CommitCleantoNativeGateway.php');foreach(['CommitCleantoLocks::create','CommitLegacyGateway::execute','CommitCleantoWriteCallback::forTenant'] as $name){if(!str_contains($src,$name))throw new Exception('missing '.$name);} $src=file_get_contents($root.'/core/app/Booking/Public/Redemption/CommitOrchestrator.php');if(!str_contains($src,'return RedeemTransaction::run')||!str_contains($src,'finally'))throw new Exception('locking boundary missing');
echo "PASS pass41_native_gateway_holds_locks\n";
