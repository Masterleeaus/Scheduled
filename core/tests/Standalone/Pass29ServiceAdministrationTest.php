<?php
declare(strict_types=1);
require __DIR__.'/../../../private/booking-sidecar/CleantoServiceAdministration.php';
use Titan\BookingSidecar\CleantoServiceAdministration;
$checks=0;
function yes($cond,$msg){global $checks;if(!$cond)throw new \RuntimeException($msg);$checks++;}
$good=CleantoServiceAdministration::input(['title'=>'  Puppy grooming  ','description'=>'  Gentle groom  '],true);
yes($good['title']==='Puppy grooming'&&$good['description']==='Gentle groom','trim');
yes($good['id']===null,'create ID');
yes(CleantoServiceAdministration::input(['id'=>17,'title'=>'Yoga','description'=>''],false)['id']===17,'update id');
$bad=[ [['title'=>''],true], [['title'=>str_repeat('A',121)],true], [['title'=>'A','other'=>'x'],true], [['title'=>'A','id'=>1],true], [['title'=>'A','description'=>str_repeat('x',2001)],true], [['id'=>0,'title'=>'A'],false], [['id'=>'2','title'=>'A'],false], [['title'=>'A'],false] ];
foreach($bad as [$v,$create]){try{CleantoServiceAdministration::input($v,$create);throw new \RuntimeException('Accepted invalid input');}catch(\InvalidArgumentException $e){$checks++;}}
$root=dirname(__DIR__,3);
$dispatcher=file_get_contents($root.'/private/booking-sidecar/Dispatcher.php');
$handler=file_get_contents($root.'/private/booking-sidecar/RequestHandler.php');
$client=file_get_contents($root.'/core/app/Booking/InternalBookingClient.php');
foreach(['owner_service_list','owner_service_create','owner_service_update'] as $op){yes(str_contains($dispatcher,$op)&&str_contains($handler,$op)&&str_contains($client,$op),'Operation wiring '.$op);}
$admin=file_get_contents($root.'/core/app/Http/Controllers/Admin/SiteCleantoServiceAdminController.php');
yes(str_contains($admin,"TenantAccess::require")&&str_contains($admin,'availableHosts')&&str_contains($admin,'forOwner'),'tenant auth');
$service=file_get_contents($root.'/private/booking-sidecar/CleantoServiceAdministration.php');
yes(str_contains($service,'service_management_enabled')&&str_contains($service,'beginTransaction')&&str_contains($service,'rollBack'),'transaction and release gate');
yes(str_contains($service,"'D'")&&str_contains($service,'SELECT DATABASE()'),'disabled default and DB identity');
echo "PASS29: $checks assertions passed\n";
