<?php
namespace App\Services\Sites;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
/** Optional AI copy generation; never creates claims, imagery or published records by itself. */
final class SiteCreationAI {
 public static function generate(array $doc,array $profile,string $template): array {
  if(!BrandProvider::ready())throw ValidationException::withMessages(['generation_mode'=>'Configure SITE_BRAND_AI_KEY before using AI-assisted creation. Select Manual to continue without AI.']);
  $prompt=['business_name'=>$profile['business_name'],'summary'=>$profile['business_summary']??'', 'service_area'=>$profile['service_area']??'', 'tone'=>$profile['tone'],'template'=>$template, 'business_vertical'=>BusinessVerticals::forAi($profile['vertical_key']??null), 'existing_services'=>$doc['services']];
  $result=Http::withToken(config('services.site_brand_ai.key'))->acceptJson()->timeout(55)->retry(1,350)->post('https://api.openai.com/v1/chat/completions',[
   'model'=>config('services.site_brand_ai.text_model','gpt-4.1-mini'),
   'response_format'=>['type'=>'json_object'],
   'messages'=>[
    ['role'=>'system','content'=>'Write factual, conservative website draft copy. Return a JSON object containing only headline (max 180 chars), tagline (max 400), about (max 3000), seo_description (max 300), services (array of up to 6 objects with name max 120 and description max 500), and faqs (array of up to 6 objects with question max 180 and answer max 1000). Use the specified business vertical for terminology and scope. Do not give personalised medical, nutrition or legal advice or imply qualifications for regulated work. Base claims only on the provided business profile; never invent testimonials, certifications, staff, awards, pricing or guarantees.'],
    ['role'=>'user','content'=>json_encode($prompt,JSON_THROW_ON_ERROR)]
   ]
  ]);
  if(!$result->successful())throw ValidationException::withMessages(['generation_mode'=>'AI provider unavailable (HTTP '.$result->status().'). Select Manual to continue.']);
  $content=$result->json('choices.0.message.content');
  if(!is_string($content)||strlen($content)>16000)throw ValidationException::withMessages(['generation_mode'=>'AI returned invalid content.']);
  $fields=json_decode($content,true,16);
  if(!is_array($fields)||array_diff(array_keys($fields),['headline','tagline','about','seo_description','services','faqs']))throw ValidationException::withMessages(['generation_mode'=>'AI returned unsupported fields.']);
  foreach(['headline','tagline','about','seo_description'] as $key)if(array_key_exists($key,$fields)){
   if(!is_string($fields[$key]))throw ValidationException::withMessages(['generation_mode'=>'AI returned invalid '.$key]);
   $doc[$key]=$fields[$key];
  }
  foreach(['services','faqs'] as $key)if(array_key_exists($key,$fields))$doc[$key]=$fields[$key];
  // Canonical validation prevents unexpected fields and malformed AI structures reaching storage.
  return SiteDocument::decode(SiteDocument::encode($doc),$template);
 }
}
