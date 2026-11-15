<?php
$root=dirname(__DIR__,2);
require $root.'/app/Services/Sites/VerticalBooking.php';
use App\Services\Sites\VerticalBooking;
$names=['pawfy','stevenwatkins','yoga-pilates','carzone'];
foreach($names as $name){
 if(!VerticalBooking::enabled($name))throw new RuntimeException('Not enabled: '.$name);
 $details=VerticalBooking::presentation($name,null);
 foreach(['title','intro','notes','action','subject'] as $key) if(empty($details[$key]))throw new RuntimeException('Missing '.$name.':'.$key);
 $preset=json_decode(file_get_contents($root.'/app/Services/Sites/Presets/'.$name.'.json'),true,512,JSON_THROW_ON_ERROR);
 if($preset['cta_url']!=='#booking')throw new RuntimeException('Missing booking CTA: '.$name);
}
if(VerticalBooking::enabled('residential'))throw new RuntimeException('Cleaning templates unexpectedly overridden');
echo "4 industry themes have working booking-request presentation configuration.\n";
