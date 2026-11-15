<?php
/** Executable, dependency-light cross-vertical acceptance checks. */
$root=dirname(__DIR__,2);
function resource_path(string $relative=''):string {global $root;return $root.'/resources'.($relative?'/'.$relative:'');}
require_once $root.'/app/Services/Sites/BusinessVerticals.php';
require_once $root.'/app/Services/Sites/BookingVerticalPresentation.php';
use App\Services\Sites\BusinessVerticals;
use App\Services\Sites\BookingVerticalPresentation;
$catalog=BusinessVerticals::all();
$cap=json_decode(file_get_contents($root.'/resources/data/vertical-booking-capabilities.json'),true,512,JSON_THROW_ON_ERROR);
if(count($catalog)!==50||count($cap['verticals'])!==50)throw new RuntimeException('Not all 50 verticals covered');
if(!BookingVerticalPresentation::supportsAllCatalogueGroups())throw new RuntimeException('Group profile incomplete');
foreach($cap['verticals'] as $row){
 $key=$row['vertical_key'];
 if(!isset($catalog[$key])||$row['recommended_template']!==$catalog[$key]['recommended_template'])throw new RuntimeException('Catalog mismatch: '.$key);
 $labels=BookingVerticalPresentation::forVertical($key,$row['recommended_template']);
 foreach(['service','staff','units','address','title','schedule','pricing','size','duration'] as $field){
  if(empty($labels[$field]))throw new RuntimeException("Missing $field for $key");
 }
 if($catalog[$key]['group']!=='Cleaning services' && $catalog[$key]['group']!=='Specialist') {
   foreach(['service','staff','title'] as $field)if(str_contains(strtolower($labels[$field]),'cleaning')||str_contains(strtolower($labels[$field]),'cleaner'))throw new RuntimeException("Cleaning terminology leaked for $key in $field");
 }
}
foreach(['dog-walking'=>'Number of dogs','car-detailing'=>'Number of vehicles','yoga-and-pilates'=>'Number of participants','tutoring'=>'Number of students'] as $key=>$expected){
 if(BookingVerticalPresentation::forVertical($key,$catalog[$key]['recommended_template'])['units']!==$expected)throw new RuntimeException('Wrong units for '.$key);
}
$view=file_get_contents($root.'/resources/views/front/partials/titan-booking-widget.blade.php');
foreach(['verticalBookingLabels','data-staff-noun','data-booking-form','data-booking-checkout-form'] as $needle)if(!str_contains($view,$needle))throw new RuntimeException('Missing UI contract: '.$needle);
$show=file_get_contents($root.'/resources/views/front/sites/show.blade.php');
if(!str_contains($show,'SiteBookingIntegration::labels')||!str_contains($show,'vertical_key'))throw new RuntimeException('Vertical key not passed into booking presentation');
$js=file_get_contents(dirname($root).'/assets/front/js/titan-booking-widget.js');
if(!str_contains($js,'dataset.staffNoun'))throw new RuntimeException('Dynamic staff prompt missing');
echo "PASS: 50 verticals; 10 groups; 50 booking profiles; UI bindings and role-specific fallback checks\n";
