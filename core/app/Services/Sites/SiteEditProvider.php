<?php
namespace App\Services\Sites;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
/** Provider generates typed proposals; it cannot write records or publish. */
final class SiteEditProvider {
 public static function propose(array $doc,string $template,string $instruction): array {
  if(!BrandProvider::ready())throw ValidationException::withMessages(['assistant'=>'Configure SITE_BRAND_AI_KEY to use the website assistant.']);
  $theme=ThemeFields::forTemplate($template);
  $context=['text'=>array_intersect_key($doc,array_flip(['headline','tagline','about','cta_label','cta_url','contact_email','phone','seo_title','seo_description'])),
   'theme_copy'=>$doc['theme_copy']??[],'allowed_theme_fields'=>array_keys($theme['copy']??[]),
   'collections'=>array_intersect_key($doc,array_flip(['services','faqs','testimonials','team','gallery'])),
   'instruction'=>$instruction];
  $response=Http::withToken(config('services.site_brand_ai.key'))->acceptJson()->timeout(55)->retry(1,300)
   ->post('https://api.openai.com/v1/chat/completions',[
    'model'=>config('services.site_brand_ai.text_model','gpt-4.1-mini'),'temperature'=>0.2,'response_format'=>['type'=>'json_object'],
    'messages'=>[
     ['role'=>'system','content'=>'You edit a business website with safe JSON operations. Return ONLY {"operations":[...],"rationale":"..."}. Maximum 12 operations. Supported operations: {"action":"set_text","field":"headline|tagline|about|cta_label|cta_url|contact_email|phone|seo_title|seo_description","value":"..."}; {"action":"set_theme_text","field":"one of allowed_theme_fields","value":"..."}; {"action":"replace_item","collection":"services|faqs|testimonials|team|gallery","index":0,"field":"a valid field of that collection","value":"..."}; {"action":"append_item","collection":"services|faqs|testimonials|team|gallery","item":{"...":"..."}}. Existing fields: services(name,description), faqs(question,answer), testimonials(name,quote), team(name,role), gallery(url,alt). Do not invent URLs, addresses, customer quotes, certifications, claims, or personal contact details. If a request cannot be fulfilled with supported operations return empty operations. Never include HTML, JavaScript or executable code.'],
     ['role'=>'user','content'=>json_encode($context,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE)]],
   ]);
  if(!$response->successful())throw ValidationException::withMessages(['assistant'=>'AI provider request failed (HTTP '.$response->status().').']);
  $parsed=json_decode((string)$response->json('choices.0.message.content'),true,24);
  if(!is_array($parsed)||!isset($parsed['operations'])||!is_array($parsed['operations'])||!count($parsed['operations']))
   throw ValidationException::withMessages(['assistant'=>'The request needs editing capabilities not yet supported by this assistant.']);
  $operations=SiteEditOperations::validate($doc,$parsed['operations'],$template);
  return ['operations'=>$operations,'rationale'=>mb_substr(is_string($parsed['rationale']??null)?$parsed['rationale']:'',0,1200)];
 }
}
