<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{CatalogueIdentifier,CatalogueLabel,CatalogueRows,CatalogueRelations,CatalogueResponse,CatalogueStaffRows};
function ok(bool $value):void {if(!$value)throw new RuntimeException('Assertion failed');echo "PASS\n";}
function reject(callable $callback):void {try{$callback();}catch(InvalidArgumentException|DomainException $e){echo "PASS\n";return;}throw new RuntimeException('Expected rejection');}
reject(fn()=>CatalogueRelations::validate([['id'=>1]], [['id'=>4,'service_id'=>2]], []));
