<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
/** Strict data boundary between an agent response and editable document state. */
final class BrandProposal {
 public static function validate(array $document,array $changes,string $template): array {
  $allowed=['brand','seo_image','theme_image','operations'];
  foreach(array_keys($changes) as $key) if(!in_array($key,$allowed,true)) self::invalid();
  if(isset($changes['operations'])) $changes['operations']=SiteEditOperations::validate($document,$changes['operations'],$template);
  if(isset($changes['brand'])) {
   if(!is_array($changes['brand'])) self::invalid();
   $candidate=$document;$candidate['brand']=$changes['brand']; SiteBranding::normalize($candidate);
   $changes['brand']=$candidate['brand'];
  }
  if(isset($changes['seo_image']) && !self::validLocalImage($changes['seo_image'])) self::invalid();
  if(isset($changes['theme_image'])) {
   $image=$changes['theme_image'];
   if(!is_array($image)||count($image)!==2||!isset($image['field'],$image['url']) || !is_string($image['field'])||!self::validLocalImage($image['url']))self::invalid();
   $fields=ThemeFields::forTemplate($template);
   if(!array_key_exists($image['field'],$fields['images']??[]))self::invalid();
  }
  return $changes;
 }
 public static function apply(array $doc,array $changes,string $template): array {
  $changes=self::validate($doc,$changes,$template);
  if(isset($changes['operations'])) $doc=SiteEditOperations::apply($doc,$changes['operations'],$template);
  if(isset($changes['brand'])) {$doc['brand']=$changes['brand'];SiteBranding::normalize($doc);}
  if(isset($changes['seo_image']))$doc['seo_image']=$changes['seo_image'];
  if(isset($changes['theme_image']))$doc['theme_images'][$changes['theme_image']['field']]=$changes['theme_image']['url'];
  return SiteDocument::decode(SiteDocument::encode($doc),$template);
 }
 private static function validLocalImage($url): bool {return is_string($url) && preg_match('~^/uploads/site-media/[1-9][0-9]*/[a-f0-9]{40}\.(?:png|jpg|webp)$~D',$url)===1;}
 private static function invalid(): void {throw ValidationException::withMessages(['proposal'=>'Invalid or unsupported proposed change.']);}
}
