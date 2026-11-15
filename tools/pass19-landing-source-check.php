<?php
$root=dirname(__DIR__).'/core';
function source(string $file): string {
 global $root;
 $path=$root.'/'.$file;
 if (!is_file($path)) { throw new RuntimeException('Missing file: '.$path); }
 return file_get_contents($path);
}
$front=source('app/Http/Controllers/Front/LandingCampaignController.php');
$admin=source('app/Http/Controllers/Admin/LandingCampaignController.php');
$checks=[
 ['admin snapshot publish',str_contains($admin,'PublishedCampaign::snapshot($row)')],
 ['public snapshot render',str_contains($front,'PublishedCampaign::render($campaign)')],
 ['public published slug',substr_count($front,"where('published_slug',".'$slug'.")")===2],
 ['migration backfill',str_contains(source('database/migrations/2026_11_11_190001_add_published_snapshots_to_titan_landing_campaigns.php'),'chunkById')],
 ['campaign form fields',str_contains(source('resources/views/admin/landing/edit.blade.php'),'form_heading')],
 ['public form fields',str_contains(source('resources/views/front/landing/show.blade.php'),"\$design['form_heading']")],
];
$fail=0;
foreach($checks as [$label,$passed]){echo ($passed?'PASS':'FAIL')." $label\n";if(!$passed)$fail++;}
exit($fail?1:0);
