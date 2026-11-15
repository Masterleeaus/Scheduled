<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
$valid=['owner_id'=>3,'booking_writes_enabled'=>true,'booking_schema_verified'=>true,'booking_rate_mapping_verified'=>true,'booking_concurrency_verified'=>true,'verified_pricing_settings'=>[]];
App\Booking\Public\Redemption\CommitCleantoTenantPolicy::validate(3,$valid);
foreach(['booking_writes_enabled','booking_schema_verified','booking_rate_mapping_verified','booking_concurrency_verified'] as $flag){$bad=$valid;$bad[$flag]=false;try{App\Booking\Public\Redemption\CommitCleantoTenantPolicy::validate(3,$bad);throw new Exception('accepted '.$flag);}catch(DomainException $e){}}
echo "PASS pass41_owner_flags_required\n";
