<?php
declare(strict_types=1);
require_once __DIR__.'/../../app/Services/Sites/SiteCleantoServiceReadiness.php';
use App\Services\Sites\SiteCleantoServiceReadiness as Checker;
function ensure(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
$catalogue=['services'=>[['id'=>1,'label'=>'Dog grooming'],['id'=>2,'label'=>'Yoga session']],
 'methods'=>[['id'=>11,'service_id'=>1,'label'=>'Hourly']],
 'units'=>[['id'=>21,'method_id'=>11,'label'=>'One pet']],
 'staff'=>[['id'=>31,'label'=>'Professional']]];
$result=Checker::inspect($catalogue,[1]);
ensure($result['ready_for_admin_review'],'Fully configured service should pass structural checks');
ensure($result['counts']['staff']===1,'Count staff from verified catalogue');
ensure($result['services'][0]['method_count']===1,'Method relation');
ensure($result['services'][0]['unit_count']===1,'Unit relation');
$result=Checker::inspect($catalogue,[2]);ensure(!$result['ready_for_admin_review'],'Missing pricing method must fail');
ensure(str_contains(implode(' ',$result['issues']),'pricing method'),'Must describe pricing method');
$result=Checker::inspect($catalogue,[999]);ensure(!$result['ready_for_admin_review'],'Stale service must fail');
ensure($result['services'][0]['status']==='missing','Missing ID must be explicit');
$result=Checker::inspect($catalogue,[]);ensure(!$result['ready_for_admin_review'],'Empty mappings must fail');
$result=Checker::inspect($catalogue,[1,1]);ensure(!$result['ready_for_admin_review'],'Duplicate mappings must fail');
$noStaff=$catalogue;$noStaff['staff']=[];
ensure(!Checker::inspect($noStaff,[1])['ready_for_admin_review'],'Missing staff must fail');
$noUnits=$catalogue;$noUnits['units']=[];
ensure(!Checker::inspect($noUnits,[1])['ready_for_admin_review'],'Missing units must fail');
$methodWrong=$catalogue;$methodWrong['methods']=[['id'=>12,'service_id'=>2,'label'=>'Other']];
ensure(!Checker::inspect($methodWrong,[1])['ready_for_admin_review'],'Only same-service methods count');
ensure(count(Checker::inspect($catalogue,[1,2])['services'])===2,'All selected services must be assessed');
ensure(count(Checker::inspect($catalogue,[1])['limitations'])===3,'Must disclose limitations');
echo "PASS28: 10 booking configuration evidence assertions passed\n";
