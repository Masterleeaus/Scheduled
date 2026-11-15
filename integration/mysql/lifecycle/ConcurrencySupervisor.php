<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Prevent an acceptance barrier from releasing when either worker has exited. */
final class ConcurrencySupervisor {
 private static function validateHandles(array $handles):void {
  if(count($handles)!==2)throw new \InvalidArgumentException('Two worker handles required');
  $seen=[];
  foreach($handles as $h){
   if(!is_array($h)||!isset($h[0],$h[1])||!is_resource($h[0])||!is_array($h[1])
    ||!isset($h[1][1],$h[1][2])||!is_resource($h[1][1])||!is_resource($h[1][2]))
    throw new \InvalidArgumentException('Invalid worker handle');
   $id=get_resource_id($h[0]);
   if(isset($seen[$id]))throw new \InvalidArgumentException('Duplicate worker handle');
   $seen[$id]=true;
  }
 }
 private static function assertAlive(array $handles):void {
  foreach($handles as $h){
   $status=proc_get_status($h[0]);
   if(!is_array($status)||empty($status['running']))
    throw new \RuntimeException('Worker exited before barrier release');
  }
 }
 public static function waitReady(string $dir,array $handles,int $timeoutMs=10000):void {
  ConcurrencyProtocol::directory($dir);
  if($timeoutMs<1||$timeoutMs>120000)throw new \InvalidArgumentException('Unsafe supervisor timeout');
  self::validateHandles($handles);
  $start=microtime(true);
  do {
   self::assertAlive($handles);
   if(ConcurrencyBarrier::bothReady($dir)){
    self::assertAlive($handles);
    return;
   }
   usleep(30000);
  }while((microtime(true)-$start)*1000<$timeoutMs);
  throw new \RuntimeException('Concurrent workers did not reach ready barrier');
 }
 /** Handles are mandatory for the acceptance runner; optional for legacy callers. */
 public static function release(string $dir,array $handles=[]):void {
  ConcurrencyProtocol::directory($dir);
  if($handles!==[]){self::validateHandles($handles);self::assertAlive($handles);}
  if(!ConcurrencyBarrier::bothReady($dir))throw new \RuntimeException('Both workers must be ready before release');
  if($handles!==[])self::assertAlive($handles);
  if(is_link($dir.'/GO')||is_file($dir.'/GO'))throw new \RuntimeException('Concurrency already released');
  ConcurrencyProtocol::createMarker($dir,'GO','go');
 }
}
