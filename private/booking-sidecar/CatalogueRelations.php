<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueRelations {public static function validate(array $services,array $methods,array $units):void {$s=array_fill_keys(array_column($services,'id'),true);$m=[];foreach($methods as $row){if(!isset($s[$row['service_id']]))throw new \DomainException('Orphaned service method');$m[$row['id']]=$row['service_id'];}foreach($units as $row){if(!isset($m[$row['method_id']])||$m[$row['method_id']]!==$row['service_id'])throw new \DomainException('Orphaned service unit');}}}
