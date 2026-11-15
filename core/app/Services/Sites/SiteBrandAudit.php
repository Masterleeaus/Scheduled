<?php
namespace App\Services\Sites;

/** Deterministic, explainable readiness checks. Not a generative AI provider. */
final class SiteBrandAudit {
    private static function luminance(string $hex): float {
        $parts = [hexdec(substr($hex,1,2)),hexdec(substr($hex,3,2)),hexdec(substr($hex,5,2))];
        $linear = array_map(static function($value) { $value/=255;return $value<=0.04045?$value/12.92:pow(($value+0.055)/1.055,2.4); },$parts);
        return 0.2126*$linear[0]+0.7152*$linear[1]+0.0722*$linear[2];
    }
    public static function contrast(string $a,string $b): float {
        $aa=self::luminance($a);$bb=self::luminance($b);
        return (max($aa,$bb)+0.05)/(min($aa,$bb)+0.05);
    }
    public static function inspect(array $doc,string $template): array {
        $brand=array_merge(SiteBranding::defaults($doc),$doc['brand']??[]);
        $issues=[];
        if(self::contrast($brand['ink'],$brand['background'])<4.5)
            $issues[]=['level'=>'high','code'=>'body_contrast','message'=>'Text/background contrast is below 4.5:1.'];
        if(self::contrast('#ffffff',$brand['primary'])<4.5)
            $issues[]=['level'=>'high','code'=>'button_contrast','message'=>'Primary brand colour has weak readable-text contrast.'];
        if(trim($doc['seo_description']??'')==='') $issues[]=['level'=>'medium','code'=>'seo_description','message'=>'Search description is missing.'];
        if(empty($doc['seo_image'])) $issues[]=['level'=>'medium','code'=>'social_image','message'=>'Social sharing image is missing.'];
        if(empty($doc['contact_email']) && empty($doc['phone'])) $issues[]=['level'=>'high','code'=>'contact','message'=>'Add a public contact method.'];
        foreach(($doc['gallery']??[]) as $i=>$item) if(trim($item['alt']??'')==='')
            $issues[]=['level'=>'medium','code'=>'gallery_alt_'.$i,'message'=>'Gallery image '.($i+1).' needs alternative text.'];
        foreach(($doc['image_blocks']??[]) as $i=>$item) if(trim($item['alt']??'')==='')
            $issues[]=['level'=>'medium','code'=>'block_alt_'.$i,'message'=>'Inserted image '.($i+1).' needs alternative text.'];
        $defs=ThemeFields::forTemplate($template);
        if(!empty($defs['images'])) {
            foreach($defs['images'] as $key=>$default) if(empty($doc['theme_images'][$key]))
                $issues[]=['level'=>'info','code'=>'stock_'.$key,'message'=>'Review template image: '.$key.'.'];
        }
        return ['issues'=>$issues,'high'=>count(array_filter($issues,fn($i)=>$i['level']==='high')),
            'medium'=>count(array_filter($issues,fn($i)=>$i['level']==='medium'))];
    }
}
