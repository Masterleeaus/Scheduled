<?php
require __DIR__.'/../core/app/Services/Landing/CampaignTemplates.php';
require __DIR__.'/../core/app/Services/Landing/CampaignDesign.php';
foreach(array_keys(\App\Services\Landing\CampaignTemplates::definitions()) as $key){
 $d=\App\Services\Landing\CampaignDesign::defaults($key);
 if(\App\Services\Landing\CampaignDesign::validate($d)!==$d)exit(1);
 if(\App\Services\Landing\CampaignDesign::decode(json_encode($d),$key)!==$d)exit(2);
 echo "OK $key\n";
}
