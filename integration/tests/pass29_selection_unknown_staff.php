<?php
declare(strict_types=1);
foreach(glob(__DIR__.'/../../core/app/Booking/Widget/Catalog*.php') as $file) require_once $file;
use App\Booking\Widget\{CatalogId,CatalogLabel,CatalogService,CatalogMethod,CatalogUnit,CatalogStaff,CatalogUnique,CatalogRelations,CatalogLimits,CatalogSnapshot,CatalogSelection,CatalogSafeJson,CatalogScope};
function eq($a,$b):void {if($a!==$b)throw new RuntimeException('Unexpected value');}
function reject(callable $f):void {try{$f();}catch(InvalidArgumentException|DomainException $e){return;}throw new RuntimeException('Expected validation to reject');}
function fixture():array{return CatalogSnapshot::build(['services'=>[['id'=>1,'title'=>'Home cleaning']],'methods'=>[['id'=>2,'service_id'=>1,'title'=>'Rooms']],'units'=>[['id'=>3,'method_id'=>2,'title'=>'Room']],'staff'=>[['id'=>4,'name'=>'Team A']]]);}
$s=fixture();reject(fn()=>CatalogSelection::verify($s,1,2,3,[8]));
echo 'PASS pass29_selection_unknown_staff'.PHP_EOL;
