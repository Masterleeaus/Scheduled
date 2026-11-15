<?php
$base=dirname(__DIR__);$assertions=0;
function check77($ok,$desc){global $assertions;if(!$ok){fwrite(STDERR,"FAIL: $desc\n");exit(1);} $assertions++;}
$doc=file_get_contents($base.'/core/app/Services/Sites/SiteDocument.php');
foreach(['testimonials','gallery','team','services','faqs'] as $key)check77(strpos($doc,"'".$key."'")!==false,'schema '.$key);
$routes=file_get_contents($base.'/core/routes/web.php');
foreach(['front.sites.lead','admin.sites.leads','throttle:5,1'] as $key)check77(strpos($routes,$key)!==false,'route '.$key);
foreach(['features','lead-form'] as $key)check77(file_exists($base.'/core/resources/views/front/sites/partials/'.$key.'.blade.php'),'partial '.$key);
foreach(['residential','commercial','end-of-lease','carpet','windows','deep','pressure','shortstay','office','multiservice'] as $key){$data=json_decode(file_get_contents($base.'/core/app/Services/Sites/Presets/'.$key.'.json'),true);check77(is_array($data),'json '.$key);foreach(['testimonials','gallery','team'] as $feature)check77(array_key_exists($feature,$data),'feature '.$key.' '.$feature);}
$controller=file_get_contents($base.'/core/app/Http/Controllers/Front/TitanSiteController.php');
foreach(['published_document',"where('published',true)",'SiteLeadValidator::check','consented_at'] as $token)check77(strpos($controller,$token)!==false,'lead '.$token);
check77(file_exists($base.'/core/database/migrations/2026_11_10_000005_create_titan_site_leads.php'),'migration');
check77(file_exists($base.'/core/public/assets/sites/site-editor.js'),'structured editor');
echo "PASS $assertions site feature contracts\n";
