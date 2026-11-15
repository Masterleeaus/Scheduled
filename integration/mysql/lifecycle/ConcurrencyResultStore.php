<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** One immutable, bounded, atomically published result per worker. */
final class ConcurrencyResultStore {
 public static function write(string $dir,string $worker,array $result):void {
  ConcurrencyProtocol::worker($worker);ConcurrencyProtocol::directory($dir);ConcurrencyResult::assertShape($result);
  $encoded=json_encode($result,JSON_THROW_ON_ERROR);
  if(strlen($encoded)>4096)throw new \DomainException('Worker result too large');
  $target=$dir.'/result-'.$worker.'.json';
  if(file_exists($target)||is_link($target))throw new \RuntimeException('Worker result already exists');
  $temp=$dir.'/.result-'.$worker.'-'.bin2hex(random_bytes(8));
  $handle=@fopen($temp,'x');if($handle===false)throw new \RuntimeException('Cannot allocate result');
  try {
   if(!flock($handle,LOCK_EX)||fwrite($handle,$encoded)!==strlen($encoded)||!fflush($handle))throw new \RuntimeException('Cannot persist complete worker result');
   flock($handle,LOCK_UN);fclose($handle);$handle=null;
   if(file_exists($target)||is_link($target)||!rename($temp,$target))throw new \RuntimeException('Cannot publish worker result');
  }finally{if(is_resource($handle))fclose($handle);if(is_file($temp))@unlink($temp);}
 }
 public static function read(string $dir,string $worker):array {
  ConcurrencyProtocol::worker($worker);ConcurrencyProtocol::directory($dir);
  $raw=ConcurrencyProtocol::boundedFile($dir.'/result-'.$worker.'.json');
  $res=json_decode($raw,true,8,JSON_THROW_ON_ERROR);
  if(!is_array($res))throw new \RuntimeException('Invalid worker JSON');
  ConcurrencyResult::assertShape($res);return $res;
 }
}
