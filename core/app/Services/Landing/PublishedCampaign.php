<?php
namespace App\Services\Landing;
/** Public campaign content is an immutable copy of the last explicitly published draft. */
final class PublishedCampaign {
 private const FIELDS = ['name','template','headline','subheading','cta_label','accent','phone','meta_title','meta_description','design_json'];
 public static function snapshot(object $campaign): string {
  $result=[];foreach(self::FIELDS as $field){$result[$field]=$campaign->{$field}??null;}
  return json_encode($result,JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES);
 }
 public static function render(object $campaign): object {
  $snapshot=json_decode($campaign->published_snapshot??'',true);
  if(!is_array($snapshot))throw new \RuntimeException('Published campaign snapshot missing. Run the publish snapshot migration.');
  foreach(self::FIELDS as $field){if(array_key_exists($field,$snapshot))$campaign->{$field}=$snapshot[$field];}
  $campaign->slug=$campaign->published_slug;
  return $campaign;
 }
 public static function validSlug(string $slug): bool {return (bool)preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$slug);}
}
