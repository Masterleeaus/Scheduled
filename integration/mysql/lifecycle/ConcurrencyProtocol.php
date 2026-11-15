<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Protect the two-process acceptance test's local result exchange. */
final class ConcurrencyProtocol {
 public static function worker(string $worker):void {
  if(!in_array($worker,['a','b'],true))throw new \InvalidArgumentException('Unknown concurrency worker');
 }
 public static function directory(string $dir):void {
  $parent=realpath(sys_get_temp_dir());
  $actual=realpath($dir);
  if($parent===false||$actual===false||is_link($dir)||!is_dir($dir)||dirname($actual)!==$parent
    ||!preg_match('/^titan-concurrency-[a-f0-9]{32}$/D',basename($actual)))
   throw new \InvalidArgumentException('Unsafe concurrency exchange directory');
 }
 public static function marker(string $dir,string $filename,string $expected):bool {
  self::directory($dir);$file=$dir.'/'.$filename;
  if(is_link($file)||!is_file($file))return false;
  $size=filesize($file);
  if($size===false||$size!==strlen($expected))return false;
  return file_get_contents($file)===$expected;
 }
 /** Create a short marker once; never overwrite an existing or symlinked marker. */
 public static function createMarker(string $dir,string $filename,string $value):void {
  self::directory($dir);
  if(!in_array($filename,['ready-a','ready-b','GO'],true))throw new \InvalidArgumentException('Unknown barrier marker');
  $path=$dir.'/'.$filename;
  // x mode uses O_EXCL: only one process can create this path, including for symlinks.
  $stream=@fopen($path,'x');
  if($stream===false)throw new \RuntimeException('Barrier marker already exists or cannot be created');
  try {
   if(fwrite($stream,$value)!==strlen($value)||!fflush($stream))throw new \RuntimeException('Could not commit barrier marker');
  }catch(\Throwable $e){fclose($stream);@unlink($path);throw $e;}
  fclose($stream);
 }
 public static function boundedFile(string $file,int $limit=4096):string {
  if(is_link($file)||!is_file($file))throw new \RuntimeException('Missing or unsafe exchange result');
  $size=filesize($file);
  if($size===false||$size<2||$size>$limit)throw new \RuntimeException('Invalid exchange result size');
  $raw=file_get_contents($file);
  if($raw===false||strlen($raw)!==$size)throw new \RuntimeException('Incomplete exchange result');
  return $raw;
 }
}
