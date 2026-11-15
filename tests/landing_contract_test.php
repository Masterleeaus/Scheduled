<?php
$root=dirname(__DIR__).'/core/';
$routes=file_get_contents($root.'routes/web.php');
$admin=file_get_contents($root.'app/Http/Controllers/Admin/LandingCampaignController.php');
$front=file_get_contents($root.'app/Http/Controllers/Front/LandingCampaignController.php');
$tests=[
 'public routes before catch-all'=>strpos($routes,"Route::get('/campaign/{slug}'")<strpos($routes,'// Dynamic Page Routes'),
 'admin routes behind pages permission'=>strpos($routes,"checkpermission:Pages'], function () {\n    Route::get('/landing-campaigns'")!==false,
 'admin route includes publishing'=>strpos($routes,"name('admin.landing.publish')")!==false,
 'public rate limited'=>strpos($routes,"middleware('throttle:5,1')")!==false,
 'publish concurrency lock'=>strpos($admin,'lockForUpdate()')!==false,
 'published-only lead submission'=>substr_count($front,"where('published',1)")===2,
 'honeypot'=>strpos($front,"['website']")!==false,
 'consent'=>strpos($front,"'consent'=>'accepted'")!==false,
 'lead persistence'=>strpos($front,"DB::table('titan_landing_leads')->insert")!==false,
 'preview noindex'=>strpos($admin,"noindex, nofollow")!==false,
 'slug uniqueness'=>strpos(file_get_contents($root.'database/migrations/2026_11_10_000001_create_titan_landing_campaigns.php'),"->unique()")!==false,
 'ten templates'=>substr_count(file_get_contents($root.'app/Services/Landing/CampaignTemplates.php'),"'=>['title'")===10,
];$failed=0;foreach($tests as $name=>$ok){echo ($ok?'PASS':'FAIL')." $name\n";if(!$ok)$failed++;}exit($failed?1:0);
