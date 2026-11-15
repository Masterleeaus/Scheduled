<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Explicit two-worker barrier with validated marker contents. */
final class ConcurrencyBarrier {
 public static function ready(string $dir,string $worker):void {
  ConcurrencyProtocol::directory($dir);ConcurrencyProtocol::worker($worker);
  if(is_file($dir.'/GO'))throw new \RuntimeException('Barrier already released');
  $file=$dir.'/ready-'.$worker;
  if(is_link($file))throw new \RuntimeException('Unsafe barrier marker');
  if(file_exists($file)){if(!ConcurrencyProtocol::marker($dir,'ready-'.$worker,'ready'))throw new \RuntimeException('Corrupt barrier marker');return;}
  try {ConcurrencyProtocol::createMarker($dir,'ready-'.$worker,'ready');}
  catch(\RuntimeException $e){
   // A simultaneous legitimate writer may have already created the marker.
   if(!ConcurrencyProtocol::marker($dir,'ready-'.$worker,'ready'))throw $e;
  }
 }
 public static function awaitGo(string $dir,int $timeoutMs=10000):void {
  ConcurrencyProtocol::directory($dir);
  if($timeoutMs<1||$timeoutMs>120000)throw new \InvalidArgumentException('Unsafe barrier timeout');
  $start=microtime(true);do {
   if(ConcurrencyProtocol::marker($dir,'GO','go'))return;
   if(is_file($dir.'/GO')||is_link($dir.'/GO'))throw new \RuntimeException('Corrupt release marker');
   usleep(20000);
  }while((microtime(true)-$start)*1000<$timeoutMs);
  throw new \RuntimeException('Timed out awaiting concurrency release');
 }
 public static function bothReady(string $dir):bool {
  return ConcurrencyProtocol::marker($dir,'ready-a','ready')&&ConcurrencyProtocol::marker($dir,'ready-b','ready');
 }
}
