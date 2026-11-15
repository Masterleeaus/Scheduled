<?php
declare(strict_types=1);
namespace App\Services\Sites;

/** A read-only evidence assessment of the real Cleanto catalogue.
 * This is not a booking release gate: live write, payment and concurrency approval
 * remain in the existing Cleanto booking engine.
 */
final class SiteCleantoServiceReadiness {
 public static function inspect(array $catalogue,array $selected):array {
  $services=self::index($catalogue['services']??[]);
  $methods=$catalogue['methods']??[];
  $units=$catalogue['units']??[];
  $staff=self::index($catalogue['staff']??[]);
  $rows=[];$issues=[];
  $selectedIds=[];
  foreach($selected as $value){
   if(!is_int($value)||$value<1||in_array($value,$selectedIds,true)){
    $issues[]='Stored service mapping contains an invalid or duplicate identifier.';
    continue;
   }
   $selectedIds[]=$value;
  }
  if(!$selectedIds)$issues[]='No Cleanto services are selected for this website.';
  if(!$staff)$issues[]='The Cleanto catalogue contains no active staff. Staff assignment and working hours require verification in Cleanto.';
  foreach($selectedIds as $id){
   $service=$services[$id]??null;
   if(!$service){
    $rows[]=['id'=>$id,'name'=>'Unavailable Cleanto service #'.$id,'status'=>'missing','method_count'=>0,'unit_count'=>0,'issues'=>['Service not found in the current verified Cleanto catalogue.']];
    $issues[]='Mapped service #'.$id.' is missing from the current catalogue.';
    continue;
   }
   $matchingMethods=[];
   foreach($methods as $method){if(is_array($method)&&($method['service_id']??null)===$id)$matchingMethods[]=$method;}
   $methodIds=array_fill_keys(array_map(static fn($m)=>(int)$m['id'],$matchingMethods),true);
   $matchingUnits=[];
   foreach($units as $unit){if(is_array($unit)&&isset($methodIds[$unit['method_id']??null]))$matchingUnits[]=$unit;}
   $serviceIssues=[];
   if(!$matchingMethods)$serviceIssues[]='No pricing method is configured for this service.';
   if($matchingMethods&&!$matchingUnits)$serviceIssues[]='No selectable service unit is configured for its pricing methods.';
   if(!$staff)$serviceIssues[]='No staff are present in the catalogue.';
   $rows[]=['id'=>$id,'name'=>(string)$service['label'],'status'=>$serviceIssues?'needs_configuration':'catalogue_complete',
    'method_count'=>count($matchingMethods),'unit_count'=>count($matchingUnits),'issues'=>$serviceIssues];
   foreach($serviceIssues as $issue)$issues[]='Service #'.$id.': '.$issue;
  }
  $issues=array_values(array_unique($issues));
  return ['ready_for_admin_review'=>count($issues)===0,'services'=>$rows,'issues'=>$issues,
   'counts'=>['mapped_services'=>count($selectedIds),'catalogue_services'=>count($services),
    'pricing_methods'=>count($methods),'service_units'=>count($units),'staff'=>count($staff)],
   'limitations'=>[
    'The catalogue does not provide service-specific staff assignments or working hours.',
    'The catalogue does not expose monetary prices, payment settlement or transactional booking evidence.',
    'A clear catalogue is not confirmation that bookings can be created or payments accepted.'
   ]];
 }
 private static function index(array $rows):array {
  $out=[];
  foreach($rows as $row){if(!is_array($row)||!isset($row['id'])||!is_int($row['id'])||$row['id']<1)continue;$out[$row['id']]=$row;}
  return $out;
 }
}
