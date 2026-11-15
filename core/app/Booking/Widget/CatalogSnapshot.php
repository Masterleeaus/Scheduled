<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogSnapshot {public static function build(array $raw):array {$sets=[];foreach(['services'=>'CatalogService','methods'=>'CatalogMethod','units'=>'CatalogUnit','staff'=>'CatalogStaff'] as $key=>$type){$items=$raw[$key]??[];if(!is_array($items)||!array_is_list($items))throw new \InvalidArgumentException('Invalid catalog list');$sets[$key]=[];foreach($items as $item){if(!is_array($item))throw new \InvalidArgumentException('Invalid catalog row');$sets[$key][]=(__NAMESPACE__.'\\'.$type)::fromRow($item);}CatalogUnique::assertIds($sets[$key]);}CatalogLimits::check($sets['services'],$sets['methods'],$sets['units'],$sets['staff']);CatalogRelations::verify($sets['services'],$sets['methods'],$sets['units']);return $sets;} }
