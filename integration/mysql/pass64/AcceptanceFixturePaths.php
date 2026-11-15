<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Keep test fixtures distinct, local and regular to avoid unintended data. */
final class AcceptanceFixturePaths {
 public static function fromEnvironment(array $env):array {
  $paths=[];
  foreach(['lifecycle'=>'BOOKING_LIFECYCLE_FIXTURE','concurrency'=>'BOOKING_CONCURRENCY_FIXTURE'] as $label=>$key){
   $path=$env[$key]??'';
   if(!is_string($path)||$path===''||!is_file($path)||is_link($path))throw new \DomainException('Missing regular '.$label.' fixture');
   $resolved=realpath($path);
   if($resolved===false)throw new \DomainException('Fixture path unresolved');
   $paths[$label]=$resolved;
  }
  if($paths['lifecycle']===$paths['concurrency'])throw new \DomainException('Use separate lifecycle and concurrency fixture files');
  return $paths;
 }
}
