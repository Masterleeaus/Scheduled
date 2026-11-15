<?php
require_once __DIR__.'/../core/app/Services/Sites/SiteTemplates.php';
use App\Services\Sites\SiteTemplates;
$cases=['residential','commercial','end-of-lease','carpet','windows'];$ok=0;
function verify75($cond,$label){global $ok;if(!$cond){fwrite(STDERR,"FAIL: $label\n");exit(1);} $ok++;echo "PASS: $label\n";}
$catalog=SiteTemplates::catalog();verify75(count($catalog)===10,'ten source template definitions preserved');
$headlines=[];
foreach($cases as $key){
 $d=SiteTemplates::make($key);
 verify75(is_array($d),'valid preset '.$key);
 verify75(count($d['services'])>=4,'four useful services '.$key);
 verify75(count($d['faqs'])>=3,'three real FAQs '.$key);
 verify75($d['cta_url']==='#contact','contact CTA not unverified booking checkout '.$key);
 verify75(preg_match('/^#[0-9a-fA-F]{6}$/',$d['accent'])===1,'valid accent '.$key);
 verify75(count(array_diff(array_keys($d), ['headline','tagline','accent','cta_label','cta_url','about','services','faqs','contact_email','phone','seo_title','seo_description']))===0,'document schema supported '.$key);
 foreach($d['services'] as $s)verify75(isset($s['name'],$s['description'])&&strlen($s['description'])>30,'nontrivial service '.$key);
 $headlines[]=$d['headline'];
 $blade=__DIR__.'/../core/resources/views/front/sites/themes/'.str_replace('-','_',$key).'.blade.php';
 verify75(is_file($blade),'Blade design exists '.$key);
 $html=file_get_contents($blade);
 verify75(strpos($html,'{{ $service[')!==false && strpos($html,'{{ $faq[')!==false,'editable services and FAQs rendered '.$key);
 verify75(strpos($html,"href=\"{{ \$document['cta_url']")!==false,'CTA uses document '.$key);
 verify75(is_file(__DIR__.'/../core/public/assets/sites/themes/'.$key.'.css'),'theme CSS exists '.$key);
 verify75(strpos(file_get_contents(__DIR__.'/../core/public/assets/sites/themes/'.$key.'.css'),'@media')!==false,'responsive CSS '.$key);
}
verify75(count(array_unique($headlines))===5,'five distinct headlines');
$view=file_get_contents(__DIR__.'/../core/resources/views/front/sites/show.blade.php');
verify75(strpos($view,"@include('front.sites.themes.'")!==false,'public dispatcher includes real theme view');
verify75(strpos($view,'@else')!==false,'sixth through tenth legacy layouts preserved');
echo "TOTAL $ok checks passed\n";
