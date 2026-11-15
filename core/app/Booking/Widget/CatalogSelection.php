<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogSelection {public static function verify(array $snap,int $service,int $method,int $unit,array $staffIds):void {$s=array_column($snap['services'],'id');if(!in_array($service,$s,true))throw new \InvalidArgumentException('Unknown service');$ms=array_filter($snap['methods'],static fn($r)=>$r['id']===$method&&$r['service_id']===$service);if(count($ms)!==1)throw new \InvalidArgumentException('Invalid service method');$us=array_filter($snap['units'],static fn($r)=>$r['id']===$unit&&$r['method_id']===$method);if(count($us)!==1)throw new \InvalidArgumentException('Invalid rate unit');$allowed=array_column($snap['staff'],'id');foreach($staffIds as $id){if(!in_array($id,$allowed,true))throw new \InvalidArgumentException('Unknown cleaner');}} }
