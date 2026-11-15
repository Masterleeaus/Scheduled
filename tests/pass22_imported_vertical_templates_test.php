<?php
require __DIR__.'/../core/app/Services/Sites/SiteTemplates.php';
use App\Services\Sites\SiteTemplates;
$catalog=SiteTemplates::catalog();
foreach(['pawfy','stevenwatkins','yoga-pilates','carzone'] as $key){if(!isset($catalog[$key]))throw new RuntimeException('Missing '.$key);}
if(count($catalog)!==19)throw new RuntimeException('Expected 19 site templates');
$verticals=json_decode(file_get_contents(__DIR__.'/../core/resources/data/business-verticals.json'),true,20,JSON_THROW_ON_ERROR);
if(count($verticals['verticals'])!==50)throw new RuntimeException('Expected 50 verticals');
foreach($verticals['verticals'] as $row){if(!isset($catalog[$row['recommended_template']]))throw new RuntimeException('Bad mapping '.$row['key']);}
foreach($catalog as $key=>$data){$view=__DIR__.'/../core/resources/views/front/sites/themes/'.str_replace('-','_',$key).'.blade.php';$css=__DIR__.'/../core/public/assets/sites/themes/'.$key.'.css';$preset=__DIR__.'/../core/app/Services/Sites/Presets/'.$key.'.json';if(!is_file($view)||!is_file($css)||!is_file($preset))throw new RuntimeException('Missing template part '.$key);}
echo "PASS: 19 website templates, 50 vertical mappings and required assets\n";
