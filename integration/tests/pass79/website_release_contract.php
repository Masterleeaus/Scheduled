<?php
// Source contracts are NOT database or browser tests.
$root=dirname(__DIR__,3);
$read=function(string $name)use($root):string{$p=$root.'/'.$name;if(!is_file($p))throw new RuntimeException("Missing $name");return file_get_contents($p);};
$checks=[
 ['source'=>'core/app/Services/Sites/SiteDocument.php','needle'=>'characterLength'],
 ['source'=>'core/app/Services/Sites/SiteDocument.php','needle'=>'Invalid UTF-8.'],
 ['source'=>'core/app/Http/Controllers/Front/TitanSiteController.php','needle'=>"strtolower(\$data['email'])"],
 ['source'=>'core/app/Http/Controllers/Front/LandingCampaignController.php','needle'=>"strtolower(\$fields['email'])"],
 ['source'=>'core/app/Http/Controllers/Admin/TitanSiteController.php','needle'=>'abort_unless(is_array($document),404)'],
 ['source'=>'core/routes/web.php','needle'=>'throttle:5,1'],
 ['source'=>'core/app/Http/Controllers/Front/TitanSiteController.php','needle'=>'SiteLeadValidator::check'],
 ['source'=>'core/database/migrations/2026_11_10_000003_create_titan_site_templates.php','needle'=>"unique(['language_id','slug']"],
 ['source'=>'core/database/migrations/2026_11_10_000005_create_titan_site_leads.php','needle'=>"foreign('site_id')"],
 ['source'=>'core/database/migrations/2026_11_10_000002_create_titan_landing_leads.php','needle'=>"foreign('campaign_id')"],
];
foreach($checks as $c)if(strpos($read($c['source']),$c['needle'])===false)throw new RuntimeException('Failed '.$c['source'].' : '.$c['needle']);
echo 'PASS '.count($checks)." website release source assertions\n";
