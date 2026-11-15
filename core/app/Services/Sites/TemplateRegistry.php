<?php
namespace App\Services\Sites;
use App\Services\Landing\CampaignTemplates;
/** Authoritative catalogue used by the creation flows and template audit. */
final class TemplateRegistry {
 public static function sites(): array {
  $catalog=SiteTemplates::catalog();
  foreach($catalog as $key=>&$template) {
   $template['key']=$key;
   $template['family']='website';
   $template['preset_path']=__DIR__.'/Presets/'.$key.'.json';
   $template['view']='front.sites.themes.'.str_replace('-','_',$key);
   $template['stylesheet']='assets/sites/themes/'.$key.'.css';
   $template['available']=is_file($template['preset_path']) && is_file(resource_path('views/front/sites/themes/'.str_replace('-','_',$key).'.blade.php')) && is_file(public_path($template['stylesheet']));
  }
  return $catalog;
 }
 public static function campaigns(): array {
  $catalog=CampaignTemplates::definitions();
  foreach($catalog as $key=>&$template) { $template['key']=$key;$template['family']='landing';$template['available']=true; }
  return $catalog;
 }
 public static function all(): array {return ['websites'=>self::sites(),'landing_pages'=>self::campaigns()];}
 public static function site(string $key): ?array { return self::sites()[$key]??null; }
 public static function campaign(string $key): ?array { return self::campaigns()[$key]??null; }
}
