<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
final class SiteMarketing {
 public static function normalize(array $doc): array {
  foreach (['service_areas','promotions','blog_posts'] as $key) {
   $items=$doc[$key]??[];
   if (!is_array($items) || count($items)>20 || array_keys($items)!==array_keys(array_values($items))) throw ValidationException::withMessages(['document'=>'Invalid '.$key]);
   $fields=['service_areas'=>['name'=>120,'description'=>280], 'promotions'=>['code'=>40,'description'=>280,'expires'=>10], 'blog_posts'=>['title'=>160,'summary'=>400,'body'=>5000,'slug'=>100]][$key];
   $clean=[];
   foreach ($items as $item) {
    if (!is_array($item)||array_keys($item)!==array_keys($fields)) throw ValidationException::withMessages(['document'=>'Invalid '.$key.' item']);
    foreach ($fields as $f=>$max) if(!isset($item[$f]) || !is_string($item[$f]) || strlen($item[$f])>$max) throw ValidationException::withMessages(['document'=>'Invalid '.$key.' '.$f]);
    if ($key==='promotions') {
     if(!preg_match('/^[A-Z0-9_-]{3,40}$/',$item['code']))throw ValidationException::withMessages(['document'=>'Invalid promotion code']);
     if($item['expires']!=='' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$item['expires']) || !checkdate((int)substr($item['expires'],5,2),(int)substr($item['expires'],8,2),(int)substr($item['expires'],0,4))))throw ValidationException::withMessages(['document'=>'Invalid promotion date']);
    }
    if ($key==='blog_posts' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$item['slug']))throw ValidationException::withMessages(['document'=>'Invalid blog slug']);
    $clean[]=$item;
   }
   if ($key==='promotions' && count(array_unique(array_column($clean,'code')))!==count($clean))throw ValidationException::withMessages(['document'=>'Duplicate promotion code']);
   if ($key==='blog_posts' && count(array_unique(array_column($clean,'slug')))!==count($clean))throw ValidationException::withMessages(['document'=>'Duplicate blog slug']);
   $doc[$key]=$clean;
  }
  $doc['analytics_consent']= $doc['analytics_consent']??false;
  if (!is_bool($doc['analytics_consent']))throw ValidationException::withMessages(['document'=>'Invalid analytics setting']);
  $doc['seo_image']=$doc['seo_image']??'';
  if (!is_string($doc['seo_image'])||strlen($doc['seo_image'])>250 || ($doc['seo_image']!==''&&!preg_match('~^https://[a-z0-9.-]+(?:/[a-zA-Z0-9/_\-?#=&%.]*)?$~iD',$doc['seo_image'])&&!preg_match('~^/uploads/site-media/[1-9][0-9]*/[a-f0-9]{40}\.(?:png|jpg|webp)$~D',$doc['seo_image'])))throw ValidationException::withMessages(['document'=>'Invalid social image']);
  return $doc;
 }
 public static function activePromotions(array $doc, string $today): array {return array_values(array_filter($doc['promotions']??[],function($p)use($today){return ($p['expires']??'')===''||$p['expires']>=$today;}));}
}
