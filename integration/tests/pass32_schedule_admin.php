<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/CleantoStaffAdministration.php';
use Titan\BookingSidecar\CleantoStaffAdministration as S;
function check(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);echo "PASS: $message\n";}
$valid=['staff_id'=>11,'shift_id'=>2,'start'=>'09:00:00','end'=>'17:30:00','off'=>false];
check(S::scheduleInput($valid)===[11,2,'09:00:00','17:30:00',false],'strict valid weekly row');
$cases=[['staff_id'=>0],['shift_id'=>'2'],['start'=>'25:00:00'],['end'=>'17:32:00'],['start'=>'20:00:00','end'=>'19:00:00'],['off'=>'false'],['extra'=>'1'],['start'=>'09:00'],['end'=>'00:00:00']];
foreach($cases as $i=>$replace){try{S::scheduleInput(array_replace($valid,$replace));throw new RuntimeException('Invalid payload accepted '.$i);}catch(InvalidArgumentException $e){check(true,'reject malformed payload '.$i);}}
$base=dirname(__DIR__,2);
$dispatcher=file_get_contents($base.'/private/booking-sidecar/Dispatcher.php');$handler=file_get_contents($base.'/private/booking-sidecar/RequestHandler.php');
check(str_contains($dispatcher,"'owner_staff_schedule_update'"),'dispatcher routes schedule');
check(str_contains($handler,"'owner_staff_schedule_update'"),'signed handler whitelists operation');
$admin=file_get_contents($base.'/core/app/Http/Controllers/Admin/SiteCleantoStaffController.php');
check(str_contains($admin,"TenantAccess::require")&&str_contains($admin,"->forOwner("),'owner-only signed tenant route');
$source=file_get_contents($base.'/private/booking-sidecar/CleantoStaffAdministration.php');
foreach(['schedule_management_enabled','beginTransaction','FOR UPDATE','ct_bookings','ct_schedule_breaks','ct_schedule_offtimes','readback mismatch','rollBack','status'] as $guard){check(str_contains($source,$guard),'guard '.$guard);}
$blade=file_get_contents($base.'/core/resources/views/admin/sites/cleanto-service-map.blade.php');
check(str_contains($blade,'cleanto-staff-schedule.save'),'editor form wired');
echo "Pass32 source checks complete.\n";
