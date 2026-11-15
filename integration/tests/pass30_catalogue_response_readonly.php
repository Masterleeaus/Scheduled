<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{CatalogueIdentifier,CatalogueLabel,CatalogueRows,CatalogueRelations,CatalogueResponse,CatalogueStaffRows};
function ok(bool $value):void {if(!$value)throw new RuntimeException('Assertion failed');echo "PASS\n";}
function reject(callable $callback):void {try{$callback();}catch(InvalidArgumentException|DomainException $e){echo "PASS\n";return;}throw new RuntimeException('Expected rejection');}
$r=CatalogueResponse::make([['id'=>1]], [['id'=>2,'service_id'=>1]], [['id'=>3,'service_id'=>1,'method_id'=>2]], []);ok($r['preview_only']===true && $r['booking_writes_enabled']===false);
