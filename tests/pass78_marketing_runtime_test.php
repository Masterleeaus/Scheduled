<?php
namespace Illuminate\Validation { class ValidationException extends \Exception {public static function withMessages($messages){return new self(json_encode($messages));}} }
namespace { require dirname(__DIR__).'/core/app/Services/Sites/SiteMarketing.php';
use App\Services\Sites\SiteMarketing;
$base=['service_areas'=>[['name'=>'Melbourne','description'=>'Southern suburbs']],'promotions'=>[['code'=>'SAVE10','description'=>'Mention this offer','expires'=>'2026-12-31']],'blog_posts'=>[['title'=>'Tips','summary'=>'Preparation','body'=>'Clean surfaces','slug'=>'tips']]];
$pass=0;$fail=0;
function check($ok,$label){global $pass,$fail;echo ($ok?'PASS':'FAIL')." $label\n";$ok?$pass++:$fail++;}
$r=SiteMarketing::normalize($base);check(count($r['promotions'])===1,'valid document');check($r['analytics_consent']===false,'consent defaults off');check($r['seo_image']==='','SEO image default');check(count(SiteMarketing::activePromotions($r,'2026-12-31'))===1,'offer on expiry date');check(count(SiteMarketing::activePromotions($r,'2027-01-01'))===0,'expired offer hidden');
$variants=[];
$x=$base;$x['promotions'][0]['code']='bad code';$variants['invalid code']=$x;
$x=$base;$x['promotions'][]=$base['promotions'][0];$variants['duplicate code']=$x;
$x=$base;$x['blog_posts'][]=$base['blog_posts'][0];$variants['duplicate slug']=$x;
$x=$base;$x['promotions'][0]['expires']='2026-02-31';$variants['invalid date']=$x;
$x=$base;$x['blog_posts'][0]['slug']='Bad Slug';$variants['invalid slug']=$x;
$x=$base;$x['seo_image']='javascript:alert(1)';$variants['unsafe SEO image']=$x;
$x=$base;$x['analytics_consent']='yes';$variants['invalid consent']=$x;
$x=$base;$x['service_areas'][0]['description']=str_repeat('x',281);$variants['oversized area']=$x;
$x=$base;$x['service_areas']=array_fill(0,21,$base['service_areas'][0]);$variants['oversized list']=$x;
foreach($variants as $label=>$value){try{SiteMarketing::normalize($value);check(false,$label);}catch(\Illuminate\Validation\ValidationException $e){check(true,$label);}}
echo "Result $pass passed / $fail failed\n";exit($fail?1:0);
}
