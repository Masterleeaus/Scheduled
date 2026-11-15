<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\BookingIndexMetadata;
use Titan\BookingSidecar\BookingSchemaIndexContract;
function check109(bool $condition, string $reason):void {if(!$condition)throw new RuntimeException($reason);}
function row109(string $table,string $index,int $sequence,string $column,int $nonUnique):array {
    return ['TABLE_NAME'=>$table,'INDEX_NAME'=>$index,'SEQ_IN_INDEX'=>$sequence,'COLUMN_NAME'=>$column,'NON_UNIQUE'=>$nonUnique];
}
function reject109(array $input):void {try{BookingIndexMetadata::normalize($input);}catch(RuntimeException $e){return;}throw new RuntimeException('Expected malformed metadata rejection');}
$tests=0;
$run=function(bool $condition,string $name)use(&$tests):void{check109($condition,$name);$tests++;};
$single=[row109('titan_booking_requests','PRIMARY',1,'request_key',0)];
$run(BookingIndexMetadata::normalize($single)['titan_booking_requests'][0]['unique']===true,'primary singleton');
$composite=[row109('titan_booking_requests','compound',1,'request_key',0),row109('titan_booking_requests','compound',2,'state',0)];
$mapped=BookingIndexMetadata::normalize($composite);
$run($mapped['titan_booking_requests'][0]['unique']===false,'composite leading column is not unique alone');
$run(in_array('titan_booking_requests.request_key',BookingSchemaIndexContract::absent($mapped),true),'composite cannot satisfy ledger gate');
$run($mapped['titan_booking_requests'][1]['unique']===false,'composite trailing column is not unique alone');
$run(BookingIndexMetadata::normalize([row109('ct_bookings','idx',1,'id',1)])['ct_bookings'][0]['unique']===false,'nonunique singleton');
$mix=array_merge($composite,$single);
$run(!in_array('titan_booking_requests.request_key',BookingSchemaIndexContract::absent(BookingIndexMetadata::normalize($mix)),true),'separate singleton still qualifies');
$reverse=[row109('ct_bookings','compound',2,'booking_status',0),row109('ct_bookings','compound',1,'id',0)];
$run(count(BookingIndexMetadata::normalize($reverse)['ct_bookings'])===2,'out of order metadata');
reject109([['TABLE_NAME'=>'ct_bookings','COLUMN_NAME'=>'id','NON_UNIQUE'=>0]]);$tests++;
reject109([row109('ct_bookings','idx',1,'id',0),row109('ct_bookings','idx',1,'booking_status',0)]);$tests++;
reject109([row109('ct_bookings','idx',2,'id',0)]);$tests++;
reject109([row109('ct_bookings','idx',1,'id',0),row109('ct_bookings','idx',2,'booking_status',1)]);$tests++;
$run(BookingIndexMetadata::normalize([])===[],'no indexes fail closed downstream');
$run(!in_array('ct_bookings.id',BookingSchemaIndexContract::absent(BookingIndexMetadata::normalize([row109('ct_bookings','PRIMARY',1,'id',0)])),true),'primary key accepted');
echo "PASS109: $tests index-identity assertions\n";
