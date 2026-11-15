<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyDirectory {
 public static function create(string $runId):string {
  ConcurrencyRunId::assert($runId);$parent=realpath(sys_get_temp_dir());
  if($parent===false)throw new \RuntimeException('Temporary directory unavailable');
  $path=$parent.'/titan-concurrency-'.$runId;
  if(!@mkdir($path,0700)||!is_dir($path))throw new \RuntimeException('Cannot create run directory');
  return $path;
 }
 public static function clear(string $dir):void {
  ConcurrencyProtocol::directory($dir);
  $items=scandir($dir);if($items===false)throw new \RuntimeException('Cannot inspect run directory');
  foreach($items as $name){if($name==='.'||$name==='..')continue;
   if(!preg_match('/^(GO|ready-[ab]|result-[ab]\.json|\.result-[ab]-[a-f0-9]{16})$/D',$name))throw new \RuntimeException('Unexpected concurrency artifact');
   $file=$dir.'/'.$name;if(is_link($file)||!is_file($file)||!@unlink($file))throw new \RuntimeException('Unsafe concurrency artifact');
  }
  if(!@rmdir($dir))throw new \RuntimeException('Cannot remove run directory');
 }
}
