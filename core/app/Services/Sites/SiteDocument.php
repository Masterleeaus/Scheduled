<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
final class SiteDocument {
    private static function isList(array $array): bool { return array_keys($array)===range(0,count($array)-1) || $array===[]; }
    // Keep Unicode character limits consistent on hosts without optional mbstring.
    public static function characterLength(string $text): int {
        if (function_exists('mb_strlen')) return mb_strlen($text, 'UTF-8');
        $count = preg_match_all('/./us', $text, $matches);
        if ($count === false) throw ValidationException::withMessages(['document'=>'Invalid UTF-8.']);
        return $count;
    }
    public static function rules(): array { return [
        'language_id'=>'required|integer|min:1', 'name'=>'required|string|min:2|max:120',
        'slug'=>'required|string|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|max:100',
        'template_key'=>'required|string|max:40', 'revision'=>'sometimes|required|integer|min:1',
        'document'=>'required|string|max:262144',
    ]; }
    public static function decode(string $json, ?string $template = null): array {
        if (strlen($json)>262144) throw ValidationException::withMessages(['document'=>'Document too large.']);
        $value=json_decode($json,true,32);
        if (!is_array($value) || self::isList($value)) throw ValidationException::withMessages(['document'=>'Document must be an object.']);
        $allowed=['vertical_key','headline','tagline','accent','cta_label','cta_url','about','services','faqs','testimonials','team','gallery','contact_email','phone','seo_title','seo_description','seo_image','analytics_consent','service_areas','promotions','blog_posts','theme_copy','theme_images','image_blocks','brand'];
        foreach($value as $field=>$input) if(!in_array($field,$allowed,true)) throw ValidationException::withMessages(['document'=>'Unexpected document field.']);
        $limits=['headline'=>180,'tagline'=>400,'accent'=>7,'cta_label'=>80,'cta_url'=>250,'about'=>3000,'contact_email'=>160,'phone'=>40,'seo_title'=>160,'seo_description'=>300];
        foreach($limits as $field=>$max) {
            if (!isset($value[$field])) $value[$field]='';
            if (!is_string($value[$field]) || self::characterLength($value[$field])>$max) throw ValidationException::withMessages(['document'=>'Invalid '.$field]);
        }
        if (!preg_match('/^#[0-9a-fA-F]{6}$/',$value['accent'])) throw ValidationException::withMessages(['document'=>'Invalid accent.']);
        $url=$value['cta_url'];
        if ($url!=='' && !preg_match('~^/(?!/)[a-zA-Z0-9/_\-?#=&%.]*$~D',$url) && !preg_match('~^https://[a-z0-9.-]+(?:/[a-zA-Z0-9/_\-?#=&%.]*)?$~iD',$url)) throw ValidationException::withMessages(['document'=>'Invalid CTA URL.']);
        if ($value['contact_email']!=='' && !filter_var($value['contact_email'],FILTER_VALIDATE_EMAIL)) throw ValidationException::withMessages(['document'=>'Invalid email.']);
        foreach(['services'=>['name'=>120,'description'=>500], 'faqs'=>['question'=>180,'answer'=>1000], 'testimonials'=>['name'=>100,'quote'=>600], 'team'=>['name'=>100,'role'=>140], 'gallery'=>['url'=>250,'alt'=>180]] as $list=>$fields) {
            if (!isset($value[$list]) && in_array($list,['testimonials','team','gallery'],true)) $value[$list]=[];
            if (!isset($value[$list]) || !is_array($value[$list]) || count($value[$list])>20 || !self::isList($value[$list])) throw ValidationException::withMessages(['document'=>'Invalid '.$list]);
            foreach ($value[$list] as $item) {
                if (!is_array($item) || array_keys($item)!==array_keys($fields)) throw ValidationException::withMessages(['document'=>'Invalid '.$list.' item']);
                foreach($fields as $key=>$limit) if(!is_string($item[$key]) || self::characterLength($item[$key])>$limit) throw ValidationException::withMessages(['document'=>'Invalid '.$list.' item field']);
                if ($list==='gallery' && !preg_match('~^https://[a-z0-9.-]+(?:/[a-zA-Z0-9/_\-?#=&%.]*)?$~iD',$item['url']) && !preg_match('~^/(?!/)[a-zA-Z0-9/_\-?#=&%.]*$~D',$item['url'])) throw ValidationException::withMessages(['document'=>'Invalid gallery URL.']);
            }
        }
        $value['vertical_key']=$value['vertical_key']??'';
        if(!is_string($value['vertical_key']) || strlen($value['vertical_key'])>100 || ($value['vertical_key']!==''&&!BusinessVerticals::find($value['vertical_key'])))throw ValidationException::withMessages(['document'=>'Invalid business vertical.']);
        SiteBranding::normalize($value);
        $value['image_blocks'] = SiteImages::validateBlocks($value['image_blocks'] ?? []);
        $value=SiteMarketing::normalize($value);
        if ($template !== null) ThemeFields::validate($value, $template);
        elseif (isset($value['theme_copy']) || isset($value['theme_images'])) throw ValidationException::withMessages(['document'=>'Template context required.']);
        return $value;
    }
    public static function encode(array $doc): string { return json_encode($doc,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR); }
}
