<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{CatalogueIdentifier,CatalogueLabel,CatalogueRows,CatalogueRelations,CatalogueResponse,CatalogueStaffRows};
function ok(bool $value):void {if(!$value)throw new RuntimeException('Assertion failed');echo "PASS\n";}
function reject(callable $callback):void {try{$callback();}catch(InvalidArgumentException|DomainException $e){echo "PASS\n";return;}throw new RuntimeException('Expected rejection');}
ok(CatalogueRows::normalize([['id'=>2,'title'=>'By room','service_id'=>1]],['service_id'])[0]['service_id']===1);
