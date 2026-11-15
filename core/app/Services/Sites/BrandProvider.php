<?php
namespace App\Services\Sites;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/** Fixed-origin provider adapter; keys never leave the server. */
final class BrandProvider {
 public static function ready(): bool {return (string) config('services.site_brand_ai.key')!=='';}
 public static function propose(array $document, array $audit, string $instruction): array {
  if(!self::ready()) throw ValidationException::withMessages(['ai'=>'Configure SITE_BRAND_AI_KEY before requesting AI proposals.']);
  $input=['name'=>$document['headline']??'', 'tagline'=>$document['tagline']??'', 'about'=>substr($document['about']??'',0,1200), 'brand'=>$document['brand']??[], 'issues'=>$audit['issues'],'instruction'=>$instruction];
  $response=Http::withToken(config('services.site_brand_ai.key'))->acceptJson()->timeout(55)->retry(2,350)
   ->post('https://api.openai.com/v1/chat/completions',[
    'model'=>config('services.site_brand_ai.text_model','gpt-4.1-mini'), 'temperature'=>0.3,
    'response_format'=>['type'=>'json_object'], 'messages'=>[
     ['role'=>'system','content'=>'You are a professional website branding designer. Return ONLY a JSON object with keys brand and rationale. brand may include only primary, secondary, surface, background, ink (#RRGGBB), font (system|humanist|geometric|serif), radius (square|subtle|rounded|pill), density (compact|comfortable|spacious), width (narrow|standard|wide). Do not include any other keys or instructions.'],
     ['role'=>'user','content'=>json_encode($input,JSON_THROW_ON_ERROR)]],
   ]);
  if(!$response->successful()) throw ValidationException::withMessages(['ai'=>'AI provider unavailable (HTTP '.$response->status().').']);
  $json=json_decode((string)$response->json('choices.0.message.content'),true,16);
  if(!is_array($json)||!isset($json['brand'])||!is_array($json['brand'])) throw ValidationException::withMessages(['ai'=>'AI provider returned invalid branding data.']);
  $candidate=$document;
  $candidate['brand']=array_merge($document['brand']??[],$json['brand']);
  SiteBranding::normalize($candidate);
  return ['brand'=>$candidate['brand'],'rationale'=>substr(is_string($json['rationale']??null)?$json['rationale']:'',0,1200)];
 }
 public static function image(string $description): string {
  if(!self::ready()) throw ValidationException::withMessages(['ai'=>'Configure SITE_BRAND_AI_KEY to generate images.']);
  $response=Http::withToken(config('services.site_brand_ai.key'))->acceptJson()->timeout(150)
   ->post('https://api.openai.com/v1/images/generations',[
    'model'=>config('services.site_brand_ai.image_model','gpt-image-1'),
    'prompt'=>'Create a professional photographic website visual suitable for a cleaning-services business. No overlaid text, watermarks, false company logos or invented endorsements. '.$description,
    'size'=>'1024x1024','n'=>1]);
  if(!$response->successful()) throw ValidationException::withMessages(['ai'=>'Image provider unavailable (HTTP '.$response->status().').']);
  $encoded=$response->json('data.0.b64_json');
  if(!is_string($encoded)||strlen($encoded)>25_000_000) throw ValidationException::withMessages(['ai'=>'Missing or oversized generated image.']);
  $bytes=base64_decode($encoded,true);
  if($bytes===false || strlen($bytes)>12_000_000 || strlen($bytes)<1000)throw ValidationException::withMessages(['ai'=>'Invalid generated image.']);
  $info=@getimagesizefromstring($bytes);
  if(!is_array($info) || !in_array($info['mime']??'', ['image/png','image/jpeg','image/webp'],true))throw ValidationException::withMessages(['ai'=>'Invalid generated image type.']);
  return $bytes;
 }
}
