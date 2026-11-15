<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Private source importer. This does not expose Cleanto under the document root. */
final class CommitCleantoLoader {
 private static bool $registered=false;
 public static function register(string $privateRoot):void {
  $directory=realpath($privateRoot);
  if($directory===false||!is_dir($directory))throw new \RuntimeException('Private booking engine missing');
  if(self::$registered)return;
  spl_autoload_register(static function(string $class)use($directory):void {
   $prefix='Titan\\BookingSidecar\\';
   if(strncmp($class,$prefix,strlen($prefix))!==0)return;
   $name=substr($class,strlen($prefix));
   if(!preg_match('/^[A-Za-z][A-Za-z0-9]*$/D',$name))return;
   $path=$directory.DIRECTORY_SEPARATOR.$name.'.php';
   if(is_file($path))require_once $path;
  });
  self::$registered=true;
 }
}
