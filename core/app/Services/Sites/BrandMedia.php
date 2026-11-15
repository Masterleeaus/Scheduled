<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
final class BrandMedia {
 public static function save(int $siteId,string $bytes): string {
  $info=@getimagesizefromstring($bytes);
  $types=['image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp'];
  if(!is_array($info)||!isset($types[$info['mime']??''])||strlen($bytes)>12_000_000||strlen($bytes)<1000)throw ValidationException::withMessages(['image'=>'Generated asset failed validation.']);
  $directory=public_path('uploads/site-media/'.$siteId);
  if(!is_dir($directory)&&!mkdir($directory,0755,true)&&!is_dir($directory))throw ValidationException::withMessages(['image'=>'Media storage unavailable.']);
  $name=bin2hex(random_bytes(20)).'.'.$types[$info['mime']];
  $path=$directory.'/'.$name;
  if(file_put_contents($path,$bytes,LOCK_EX)===false)throw ValidationException::withMessages(['image'=>'Media write failed.']);
  @chmod($path,0644);
  return '/uploads/site-media/'.$siteId.'/'.$name;
 }
}
