<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
/** Small, strictly typed change language; no arbitrary object keys or code execution. */
final class SiteEditOperations {
 private const TEXT_FIELDS = ['headline','tagline','about','cta_label','cta_url','contact_email','phone','seo_title','seo_description'];
 private const COLLECTIONS = ['services'=>['name','description'],'faqs'=>['question','answer'],'testimonials'=>['name','quote'],'team'=>['name','role'],'gallery'=>['url','alt']];
 private static function invalid(): void {throw ValidationException::withMessages(['assistant'=>'Unsupported, invalid or oversized website edit operation.']);}
 public static function apply(array $doc,array $operations,string $template): array {
  if(!array_is_list($operations)||count($operations)<1||count($operations)>12) self::invalid();
  foreach($operations as $op) {
   if(!is_array($op)||!isset($op['action'])||!is_string($op['action']))self::invalid();
   switch($op['action']) {
    case 'set_text':
     if(array_diff(array_keys($op),['action','field','value'])||!isset($op['field'],$op['value'])||!is_string($op['field'])||!is_string($op['value']))self::invalid();
     if(!in_array($op['field'],self::TEXT_FIELDS,true)||mb_strlen($op['value'],'UTF-8')>3000)self::invalid();
     $doc[$op['field']]=$op['value'];break;
    case 'set_theme_text':
     if(array_diff(array_keys($op),['action','field','value'])||!isset($op['field'],$op['value'])||!is_string($op['field'])||!is_string($op['value']))self::invalid();
     $fields=ThemeFields::forTemplate($template);
     if(!array_key_exists($op['field'],$fields['copy']??[])||mb_strlen($op['value'],'UTF-8')>1200)self::invalid();
     $doc['theme_copy'][$op['field']]=$op['value'];break;
    case 'replace_item':
     if(array_diff(array_keys($op),['action','collection','index','field','value'])||!isset($op['collection'],$op['index'],$op['field'],$op['value'])||!is_string($op['collection'])||!is_int($op['index'])||!is_string($op['field'])||!is_string($op['value']))self::invalid();
     $fields=self::COLLECTIONS[$op['collection']]??null;
     if(!$fields||!in_array($op['field'],$fields,true)||$op['index']<0||!isset($doc[$op['collection']][$op['index']]))self::invalid();
     $doc[$op['collection']][$op['index']][$op['field']]=$op['value'];break;
    case 'append_item':
     if(array_diff(array_keys($op),['action','collection','item'])||!isset($op['collection'],$op['item'])||!is_string($op['collection'])||!is_array($op['item']))self::invalid();
     $fields=self::COLLECTIONS[$op['collection']]??null;
     if(!$fields||array_keys($op['item'])!==$fields||count($doc[$op['collection']]??[])>=20)self::invalid();
     foreach($fields as $field)if(!is_string($op['item'][$field]))self::invalid();
     $doc[$op['collection']][]=$op['item'];break;
    default:self::invalid();
   }
  }
  return SiteDocument::decode(SiteDocument::encode($doc),$template);
 }
 public static function validate(array $doc,array $operations,string $template): array {
  self::apply($doc,$operations,$template);
  return $operations;
 }
}
