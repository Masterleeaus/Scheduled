<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
/** Generated media is scoped to the site that requested it, not just a valid URL shape. */
final class AgentMediaPolicy {
 public static function belongsToSite(string $url,int $siteId): bool {
  if($siteId<1)return false;
  return preg_match('~^/uploads/site-media/'.preg_quote((string)$siteId,'~').'/[a-f0-9]{40}\.(?:png|jpg|webp)$~D',$url)===1;
 }
 public static function requireOwnership(array $changes,int $siteId): void {
  foreach(['seo_image','theme_image'] as $key) {
   if(!array_key_exists($key,$changes))continue;
   $url=$key==='theme_image'?($changes[$key]['url']??null):$changes[$key];
   if(!is_string($url)||!self::belongsToSite($url,$siteId))throw ValidationException::withMessages(['image'=>'Proposed image does not belong to this site.']);
  }
 }
}
