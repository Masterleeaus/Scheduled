<?php
// Run: php integration/tests/pass80/release_boundary_contract.php
$root=dirname(__DIR__,3);
$read=function($path)use($root){$file="$root/$path";if(!is_file($file))throw new RuntimeException("Missing $path");return file_get_contents($file);};
$site=$read('core/app/Http/Controllers/Front/TitanSiteController.php');
$admin=$read('core/app/Http/Controllers/Admin/TitanSiteController.php');
$landing=$read('core/app/Http/Controllers/Admin/LandingCampaignController.php');
$validator=$read('core/app/Services/Sites/SiteLeadValidator.php');
$routes=$read('core/routes/web.php');
$checks=[
 'honeypot checked before website lead insert'=>strpos($site,"!empty(\$data['website'])") < strpos($site,"DB::table('titan_site_leads')->insert"),
 'honeypot acknowledged via redirect'=>preg_match('~if \(!empty\(\$data\[\x27website\x27\]\)\) \{\s*return redirect\(\)->route~',$site)===1,
 'website lead input validated'=>str_contains($site,'SiteLeadValidator::rules()'),
 'website lead checked against snapshot'=>str_contains($site,'SiteLeadValidator::check($data,$document)'),
 'honeypot still bounded'=>str_contains($validator,"'website'=>'nullable|string|max:100'"),
 'site creation checks duplicate slug within language'=>str_contains($admin,"Slug already in use for this site language.") && str_contains($admin,"->where('language_id',"),
 'site schema unique index retained'=>str_contains($read('core/database/migrations/2026_11_10_000003_create_titan_site_templates.php'),"$"."t->unique(['language_id','slug']"),
 'site updates row locked'=>str_contains($admin,'->lockForUpdate()->first()'),
 'private site lead inbox'=>str_contains($admin,"'Cache-Control','private,no-store'"),
 'private landing lead inbox'=>str_contains($landing,"'Cache-Control','private, no-store'"),
 'landing public form throttled'=>str_contains($routes,"->middleware('throttle:5,1')->name('front.landing.submit')"),
 'site public form throttled'=>str_contains($routes,"->middleware('throttle:5,1')->name('front.sites.lead')"),
 'site is explicitly not tenant isolated'=>str_contains($routes,'dedicated tenant mapping pending'),
];
$bad=0;foreach($checks as $k=>$v){echo ($v?'PASS':'FAIL')." $k\n";if(!$v)$bad++;}echo (count($checks)-$bad).'/'.count($checks)." passed\n";exit($bad?1:0);
