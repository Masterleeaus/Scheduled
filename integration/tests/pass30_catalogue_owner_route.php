<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{CatalogueIdentifier,CatalogueLabel,CatalogueRows,CatalogueRelations,CatalogueResponse,CatalogueStaffRows};
function ok(bool $value):void {if(!$value)throw new RuntimeException('Assertion failed');echo "PASS\n";}
function reject(callable $callback):void {try{$callback();}catch(InvalidArgumentException|DomainException $e){echo "PASS\n";return;}throw new RuntimeException('Expected rejection');}
$a=file_get_contents(__DIR__.'/../../core/routes/web.php');ok(str_contains($a,"/booking/catalogue")&&str_contains($a,"BookingCatalogueController@index"));
