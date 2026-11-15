<?php
require_once __DIR__.'/../core/app/Services/Sites/SiteTemplates.php';
use App\Services\Sites\SiteTemplates;
$ok=0;
function check76($condition,$message){global $ok;if(!$condition){fwrite(STDERR,"FAIL: $message\n");exit(1);} $ok++;}
$catalog=SiteTemplates::catalog();check76(count($catalog)===10,'ten themes registered');
$headlines=[];$colors=[];
foreach($catalog as $key=>$info){
 $doc=SiteTemplates::make($key);check76(is_array($doc),'valid document '.$key);
 check76(count($doc['services'])>=4,'four services '.$key);
 check76(count($doc['faqs'])>=3,'three FAQs '.$key);
 check76($doc['cta_url']==='#contact','no fake booking '.$key);
 check76(preg_match('/^#[A-Fa-f0-9]{6}$/',$doc['accent'])===1,'accent '.$key);
 check76(strlen($doc['about'])>=80,'useful about '.$key);
 check76(strlen($doc['seo_description'])>=50,'seo description '.$key);
 foreach($doc['services'] as $service){check76(strlen($service['name'])>=5&&strlen($service['description'])>=50,'service copy '.$key);}
 $view=__DIR__.'/../core/resources/views/front/sites/themes/'.str_replace('-','_',$key).'.blade.php';
 $css=__DIR__.'/../core/public/assets/sites/themes/'.$key.'.css';
 check76(is_file($view)&&is_file($css),'view and CSS '.$key);
 $markup=file_get_contents($view);check76(strpos($markup,"{{ \$document['headline'] }}")!==false,'editable title '.$key);
 check76(strpos($markup,"{{ \$service['name'] }}")!==false,'editable service '.$key);
 check76(strpos($markup,"{{ \$faq['question'] }}")!==false,'editable FAQ '.$key);
 check76(strpos($markup,"href=\"{{ \$document['cta_url']")!==false,'editable CTA '.$key);
 check76(strpos($markup,'id="contact"')!==false,'contact section '.$key);
 check76(strpos(file_get_contents($css),'@media')!==false,'responsive stylesheet '.$key);
 $headlines[]=$doc['headline'];$colors[]=$doc['accent'];
}
check76(count(array_unique($headlines))===10,'distinct titles');check76(count(array_unique($colors))===10,'distinct palettes');
$public=file_get_contents(__DIR__.'/../core/resources/views/front/sites/show.blade.php');
check76(strpos($public,'SiteTemplates::catalog()')!==false,'dispatch all registered themes');
check76(strpos($public,"@include('front.sites.themes.'")!==false,'blade includes safe catalog');
check76(SiteTemplates::make('invalid-key')===null,'reject unknown keys');
echo "PASS $ok assertions\n";
