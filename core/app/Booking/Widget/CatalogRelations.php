<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogRelations {public static function verify(array $services,array $methods,array $units):void {$s=array_fill_keys(array_column($services,'id'),true);$m=[];foreach($methods as $r){if(!isset($s[$r['service_id']]))throw new \InvalidArgumentException('Orphan method');$m[$r['id']]=true;}foreach($units as $r){if(!isset($m[$r['method_id']]))throw new \InvalidArgumentException('Orphan unit');}} }
