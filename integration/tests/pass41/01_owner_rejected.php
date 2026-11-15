<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
try{App\Booking\Public\Redemption\CommitCleantoTenantPolicy::validate(4,['owner_id'=>5]);throw new Exception('accepted');}catch(DomainException $e){if(!str_contains($e->getMessage(),'owner'))throw $e;}
echo "PASS pass41_owner_rejected\n";
