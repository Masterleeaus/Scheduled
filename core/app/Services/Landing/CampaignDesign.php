<?php
namespace App\Services\Landing;
use Illuminate\Validation\ValidationException;
/** Campaign-specific editable design system. Never accepts arbitrary user HTML. */
final class CampaignDesign {
 public static function defaults(string $template): array {
  $definition=CampaignTemplates::get($template) ?? [];
  return ['eyebrow'=>$definition['title'] ?? 'Cleaning services','image_url'=>'','image_alt'=>'','benefits'=>self::benefitsFor($template),'trust_heading'=>'Why choose us','trust_text'=>'Tell us about your property and we will propose a suitable service.','theme'=>'split','button_style'=>'rounded','secondary_colour'=>'#132b46','image_blocks'=>[],'form_heading'=>'Tell us what you need','form_intro'=>"We'll contact you to discuss your request.",'form_button'=>'Send enquiry','consent_label'=>'I consent to being contacted about this enquiry.','footer_text'=>''];
 }
 private static function benefitsFor(string $template): array {
  $byNiche=['home-cleaning'=>['Convenient home care','Flexible visits','Thoughtful cleaning'], 'commercial'=>['Workplace schedules','Reliable teams','Tailored scopes'], 'end-of-lease'=>['Detailed move-out cleaning','Flexible handover dates','Clear service scope'], 'carpet'=>['Carpet and upholstery care','Targeted treatment','Professional equipment'], 'window'=>['Interior and exterior options','Clear glass finish','Flexible appointments'], 'deep-clean'=>['Focused detail work','Kitchens and bathrooms','A fresh start'], 'pressure'=>['Exterior surface cleaning','Paths and patios','Careful site assessment'], 'short-stay'=>['Guest-ready turnovers','Scheduling coordination','Consistent presentation'], 'office'=>['Workplace hygiene','Recurring visits','Commercial attention to detail'], 'multi-service'=>['One enquiry for many services','Tailored service plans','Simple scheduling']];
  return $byNiche[$template]??['Flexible scheduling','Professional service','Clear communication'];
 }
 public static function fromInput(array $data,string $template): array {
  $base=self::defaults($template);
  $allowed=['eyebrow','image_url','image_alt','benefits','trust_heading','trust_text','theme','button_style','secondary_colour','image_blocks','form_heading','form_intro','form_button','consent_label','footer_text'];
  foreach($allowed as $key) if(array_key_exists($key,$data)) $base[$key]=$data[$key];
  return self::validate($base);
 }
 public static function decode($json,string $template): array {
  if(!is_string($json)||$json==='')return self::defaults($template);
  $decoded=json_decode($json,true);
  if(!is_array($decoded))return self::defaults($template);
  try{return self::fromInput($decoded,$template);}catch(ValidationException $e){return self::defaults($template);}
 }
 public static function validate(array $input): array {
  foreach(['eyebrow'=>90,'image_alt'=>180,'trust_heading'=>120,'trust_text'=>1200,'form_heading'=>120,'form_intro'=>400,'form_button'=>80,'consent_label'=>240,'footer_text'=>180] as $key=>$max) {
   if(!is_string($input[$key]??null)||strlen($input[$key])>$max)self::fail($key);
  }
  if(!is_array($input['benefits']??null)||count($input['benefits'])>8)self::fail('benefits');
  foreach($input['benefits'] as $b)if(!is_string($b)||!trim($b)||strlen($b)>120)self::fail('benefits');
  if(!in_array($input['theme']??null,['split','centered','editorial'],true))self::fail('theme');
  if(!in_array($input['button_style']??null,['rounded','pill','square'],true))self::fail('button_style');
  if(!is_string($input['secondary_colour']??null)||!preg_match('/^#[0-9a-fA-F]{6}$/D',$input['secondary_colour']))self::fail('secondary_colour');
  $url=$input['image_url']??null;
  if(!is_string($url)||strlen($url)>500||($url!==''&&!\App\Services\Sites\ThemeFields::safeImage($url)))self::fail('image_url');
  if(!is_array($input['image_blocks']??null) || count($input['image_blocks'])>12)self::fail('image_blocks');
  $blocks=[];
  foreach($input['image_blocks'] as $block) {
   if(!is_array($block))self::fail('image_blocks');
   $url=$block['url']??'';$alt=$block['alt']??'';$caption=$block['caption']??'';$placement=$block['placement']??'after-trust';$width=$block['width']??'normal';
   if(!is_string($url)||strlen($url)>500||!\App\Services\Sites\ThemeFields::safeImage($url))self::fail('image_blocks');
   if(!is_string($alt)||strlen($alt)>180||!is_string($caption)||strlen($caption)>240)self::fail('image_blocks');
   if(!in_array($placement,['after-hero','before-trust','after-trust','before-form'],true))self::fail('image_blocks');
   if(!in_array($width,['normal','wide','full'],true))self::fail('image_blocks');
   $blocks[]=['url'=>$url,'alt'=>$alt,'caption'=>$caption,'placement'=>$placement,'width'=>$width];
  }
  $input['image_blocks']=$blocks;
  return $input;
 }
 private static function fail(string $field): void {throw ValidationException::withMessages([$field=>'Invalid campaign design value.']);}
}
