<?php
declare(strict_types=1);
require_once __DIR__.'/../../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\CleantoServiceUnitAdministration as Units;
function check(bool $pass,string $message):void{if(!$pass)throw new RuntimeException($message);}
$cases=[
 [['unit_id'=>7,'base_price'=>'49.99','duration_minutes'=>60],true],
 [['unit_id'=>7,'base_price'=>'0','duration_minutes'=>5],true],
 [['unit_id'=>7,'base_price'=>'1000001.00','duration_minutes'=>60],false],
 [['unit_id'=>7,'base_price'=>'5.999','duration_minutes'=>60],false],
 [['unit_id'=>7,'base_price'=>49.99,'duration_minutes'=>60],false],
 [['unit_id'=>0,'base_price'=>'25.00','duration_minutes'=>60],false],
 [['unit_id'=>7,'base_price'=>'25.00','duration_minutes'=>0],false],
 [['unit_id'=>7,'base_price'=>'25.00','duration_minutes'=>1445],false],
 [['unit_id'=>7,'base_price'=>'25.00','duration_minutes'=>17],false],
 [['unit_id'=>7,'base_price'=>'25.00','duration_minutes'=>60,'status'=>'E'],false],
];
$n=0;foreach($cases as [$input,$valid]){$ok=true;try{$v=Units::input($input);check($v['unit_id']===$input['unit_id'],'ID changed');}catch(InvalidArgumentException $e){$ok=false;}check($ok===$valid,'Input validation regression '.json_encode($input));$n++;}
$root=dirname(__DIR__,2);
$sidecar=file_get_contents($root.'/../private/booking-sidecar/CleantoServiceUnitAdministration.php');
$dispatch=file_get_contents($root.'/../private/booking-sidecar/Dispatcher.php');
$handler=file_get_contents($root.'/../private/booking-sidecar/RequestHandler.php');
$controller=file_get_contents($root.'/app/Http/Controllers/Admin/SiteCleantoServiceUnitController.php');
$routes=file_get_contents($root.'/routes/web.php');
$view=file_get_contents($root.'/resources/views/admin/sites/cleanto-service-map.blade.php');

foreach(['unit_management_enabled','service_management_enabled','SELECT DATABASE()','FOR UPDATE','ct_bookings','rollBack','configured_for_booking'] as $needle){check(str_contains($sidecar,$needle),'Sidecar guard missing '.$needle);$n++;}
foreach(['owner_service_units','owner_service_unit_update'] as $needle){check(str_contains($dispatch,$needle)&&str_contains($handler,$needle),'Operation not allowlisted '.$needle);$n++;}
foreach(['TenantAccess::require','availableHosts','forOwner','owner_service_unit_update'] as $needle){check(str_contains($controller,$needle),'Controller guard missing '.$needle);$n++;}
check(str_contains($routes,'admin.sites.cleanto-unit-admin.save'),'Route missing');$n++;
check(str_contains($view,'native-unit-management')&&str_contains($view,'@csrf'),'UI missing');$n++;
echo "PASS $n/".$n." standalone assertions\n";
