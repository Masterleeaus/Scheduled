<?php
namespace App\Services\Landing;
final class CampaignInput {
 public static function rules(): array { return [
  'language_id'=>'required|integer|exists:languages,id', 'name'=>'required|string|max:120',
  'slug'=>'required|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|max:100',
  'template'=>'required|in:'.implode(',',array_keys(CampaignTemplates::definitions())),
  'headline'=>'required|string|max:180','subheading'=>'nullable|string|max:1000',
  'cta_label'=>'required|string|max:80','accent'=>'required|regex:/^#[0-9a-fA-F]{6}$/',
  'phone'=>'nullable|string|max:40','meta_title'=>'nullable|string|max:160',
  'meta_description'=>'nullable|string|max:300',
  'eyebrow'=>'nullable|string|max:90','image_url'=>'nullable|string|max:500','image_alt'=>'nullable|string|max:180',
  'benefits'=>'nullable|array|max:8','benefits.*'=>'nullable|string|max:120',
  'trust_heading'=>'nullable|string|max:120','trust_text'=>'nullable|string|max:1200',
  'theme'=>'nullable|in:split,centered,editorial','button_style'=>'nullable|in:rounded,pill,square',
  'secondary_colour'=>'nullable|regex:/^#[0-9a-fA-F]{6}$/',
  'form_heading'=>'nullable|string|max:120','form_intro'=>'nullable|string|max:400','form_button'=>'nullable|string|max:80','consent_label'=>'nullable|string|max:240','footer_text'=>'nullable|string|max:180',
  'image_blocks'=>'nullable|array|max:12','image_blocks.*.url'=>'required|string|max:500','image_blocks.*.alt'=>'nullable|string|max:180','image_blocks.*.caption'=>'nullable|string|max:240','image_blocks.*.placement'=>'required|in:after-hero,before-trust,after-trust,before-form','image_blocks.*.width'=>'required|in:normal,wide,full', 'revision'=>'nullable|integer|min:1',
 ]; }
 public static function fields(array $valid): array {
   $fields=array_intersect_key($valid,array_flip(['language_id','name','slug','template','headline','subheading','cta_label','accent','phone','meta_title','meta_description']));
  if(isset($valid['benefits']))$valid['benefits']=array_values(array_filter($valid['benefits'],static fn($item)=>is_string($item)&&trim($item)!==''));
  $fields['design_json']=json_encode(CampaignDesign::fromInput(array_intersect_key($valid,array_flip(['eyebrow','image_url','image_alt','benefits','trust_heading','trust_text','theme','button_style','secondary_colour','image_blocks','form_heading','form_intro','form_button','consent_label','footer_text'])), $valid['template']),JSON_THROW_ON_ERROR);
  return $fields;
 }
}
