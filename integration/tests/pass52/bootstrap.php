<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass51/bootstrap.php';
use Titan\BookingSidecar\BookingMutationStateProof;
use Titan\BookingSidecar\BookingCancellationInput;
use Titan\BookingSidecar\BookingMutationReader;
use Titan\BookingSidecar\BookingLegacyStatusWriter;
use Titan\BookingSidecar\BookingRescheduleWriter;
function proof52(array $rows,bool $active=true,bool $ok=true):BookingMutationStateProof {
 $db=db51($rows,$ok,1,$active); return new BookingMutationStateProof($db);
}
function row52(string $status='CC',string $start='2026-11-12 10:00:00',string $staff='2',int $id=7):array {
 return ['id'=>$id,'order_id'=>11,'booking_status'=>$status,'booking_date_time'=>$start,'staff_ids'=>$staff];
}
function good52():array{return ['window'=>['local_start'=>'2026-11-12 10:00:00'],'staff_ids'=>[2]];}
function ok52(bool $condition,string $why):void {if(!$condition)throw new RuntimeException($why);}
function fail52(callable $call,string $match):void {try{$call();}catch(Throwable $e){ok52(str_contains($e->getMessage(),$match),'Unexpected exception: '.$e->getMessage());return;}throw new RuntimeException('Expected error containing '.$match);}
