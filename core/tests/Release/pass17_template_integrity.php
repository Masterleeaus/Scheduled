<?php
declare(strict_types=1);
/* Run from any working directory; no Laravel bootstrap or database required. */
$root=dirname(__DIR__,3);
$core=$root.'/core';
require_once $core.'/app/Services/Sites/SiteTemplates.php';
require_once $core.'/app/Services/Landing/CampaignTemplates.php';
$web=\App\Services\Sites\SiteTemplates::catalog();
$landing=\App\Services\Landing\CampaignTemplates::definitions();
$fail=[];$checks=0;
foreach($web as $key=>$definition){
  foreach(['title','headline','tagline','accent'] as $field){$checks++;if(!isset($definition[$field])||!is_string($definition[$field])||$definition[$field]==='')$fail[]="$key missing $field";}
  $paths=["$core/app/Services/Sites/Presets/$key.json","$core/resources/views/front/sites/themes/".str_replace('-','_',$key).'.blade.php',"$core/public/assets/sites/themes/$key.css"];
  foreach($paths as $path){$checks++;if(!is_file($path))$fail[]="Missing $path";}
  $preset=@json_decode((string)@file_get_contents($paths[0]),true);$checks++;if(!is_array($preset))$fail[]="$key invalid JSON";
}
foreach($landing as $key=>$definition){foreach(['title','headline','subheading','cta'] as $field){$checks++;if(!isset($definition[$field])||!is_string($definition[$field])||$definition[$field]==='')$fail[]="$key missing $field";}}
$checks+=2;
if(count($web)!==15)$fail[]='Unexpected website catalogue count';
if(count($landing)!==10)$fail[]='Unexpected landing catalogue count';
if($fail){fwrite(STDERR,json_encode(['result'=>'FAIL','checks'=>$checks,'errors'=>$fail],JSON_PRETTY_PRINT)."\n");exit(1);}
echo json_encode(['result'=>'PASS','checks'=>$checks,'website_templates'=>count($web),'landing_templates'=>count($landing)],JSON_PRETTY_PRINT)."\n";
