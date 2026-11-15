<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
/** Business onboarding creates ordinary editable SiteDocument records, never a parallel builder. */
final class SiteOnboarding {
 public static function rules(): array {return [
  'business_name'=>'required|string|min:2|max:120',
  'vertical_key'=>'required|string|max:100',
  'business_summary'=>'nullable|string|max:1200',
  'service_area'=>'nullable|string|max:120',
  'contact_email'=>'nullable|email|max:160',
  'phone'=>'nullable|string|max:40',
  'tone'=>'required|in:professional,friendly,premium,straightforward',
  'generation_mode'=>'required|in:assisted,manual',
 ];}
 public static function build(string $template,array $data): array {
  $vertical=BusinessVerticals::require((string)($data['vertical_key']??''));
  $doc=SiteTemplates::make($template);
  if(!$doc) throw ValidationException::withMessages(['template_key'=>'Unknown template.']);
  $name=trim((string)($data['business_name']??''));
  if($name==='' || SiteDocument::characterLength($name)>120)throw ValidationException::withMessages(['business_name'=>'Invalid business name.']);
  $summary=trim((string)($data['business_summary']??''));
  $area=trim((string)($data['service_area']??''));
  $tone=(string)($data['tone']??'professional');
  if(!in_array($tone,['professional','friendly','premium','straightforward'],true))throw ValidationException::withMessages(['tone'=>'Invalid tone.']);
  if($summary!=='')$doc['about']=$summary;
  elseif($vertical['group']!=='Cleaning services')$doc['about']='Contact us to discuss '.$vertical['name'].' services and availability.';
  $doc['vertical_key']=$vertical['key'];
  $doc['headline']=$name;
  // Replace cleaning-only placeholder services when this site targets another profession.
  if($vertical['group']!=='Cleaning services')$doc['services']=BusinessVerticals::safeServiceSeed($vertical['key']);
  $doc['tagline']=$summary!=='' ? self::truncate($summary,400) : ($vertical['group']==='Cleaning services' ? self::truncate($doc['tagline'],400) : 'Explore '.$vertical['name'].' services in your area.');
  $doc['seo_title']=self::truncate($name.' | '.(SiteTemplates::catalog()[$template]['title']??$vertical['name']),160);
  $doc['seo_description']=self::truncate($summary!==''?$summary:$doc['tagline'],300);
  $doc['contact_email']=(string)($data['contact_email']??'');
  $doc['phone']=(string)($data['phone']??'');
  if($area!=='')$doc['service_areas']=[['name'=>$area,'description'=>'Contact us to confirm scheduling and availability in this area.']];
  return SiteDocument::decode(SiteDocument::encode($doc),$template);
 }
 public static function truncate(string $str,int $max): string {
  if(SiteDocument::characterLength($str)<=$max)return $str;
  if(function_exists('mb_substr'))return mb_substr($str,0,$max,'UTF-8');
  preg_match_all('/./us',$str,$m);return implode('',array_slice($m[0],0,$max));
 }
}
