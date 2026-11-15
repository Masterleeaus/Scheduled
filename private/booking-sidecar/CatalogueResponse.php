<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueResponse {public static function make(array $services,array $methods,array $units,array $staff):array {CatalogueRelations::validate($services,$methods,$units);return ['services'=>$services,'methods'=>$methods,'units'=>$units,'staff'=>$staff,'preview_only'=>true,'booking_writes_enabled'=>false];}}
