<?php
require __DIR__.'/../core/vendor/autoload.php';
use App\Services\Landing\CampaignDesign;
$ok=0;
foreach(\App\Services\Landing\CampaignTemplates::definitions() as $key=>$unused){
 $d=CampaignDesign::defaults($key);
 $d['image_blocks']=[['url'=>'https://example.com/example.jpg','alt'=>'Example','caption'=>'Example','placement'=>'after-hero','width'=>'wide']];
 $validated=CampaignDesign::validate($d);
 if(count($validated['image_blocks'])!==1)throw new RuntimeException('Blocks lost: '.$key);
 $ok++;
}
print("Pass16 campaign design checks: $ok/10\n");
