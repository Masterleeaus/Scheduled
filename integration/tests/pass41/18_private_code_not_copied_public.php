<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
$src=file_get_contents($root.'/core/app/Booking/Public/Redemption/CommitCleantoLoader.php');if(!str_contains($src,'realpath(')||!str_contains($src,'spl_autoload_register'))throw new Exception('Private loader absent');foreach(['public/cleanto','core/public/cleanto'] as $dir)if(is_dir($root.'/'.$dir))throw new Exception('Exposed booking engine');
echo "PASS pass41_private_code_not_copied_public\n";
