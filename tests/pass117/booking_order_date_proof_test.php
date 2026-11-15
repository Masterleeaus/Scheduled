<?php
declare(strict_types=1);
require_once __DIR__.'/../../private/booking-sidecar/BookingWriteProofOrderDate.php';
use Titan\BookingSidecar\BookingWriteProofOrderDate;
$checks=0;
function accepted($stored,$start):void {global $checks;BookingWriteProofOrderDate::check($stored,$start);$checks++;}
function denied($stored,$start):void {global $checks;try{BookingWriteProofOrderDate::check($stored,$start);}catch(DomainException $e){$checks++;return;}throw new RuntimeException('Unexpected accepted order date');}
accepted('2026-12-01','2026-12-01 08:00:00');
accepted('2027-01-01','2027-01-01 23:59:59');
denied('2026-12-02','2026-12-01 08:00:00');
denied('2026-11-30','2026-12-01 08:00:00');
denied('', '2026-12-01 08:00:00');
denied(null,'2026-12-01 08:00:00');
denied('2026-12-01 00:00:00','2026-12-01 08:00:00');
denied('2026-12-1','2026-12-01 08:00:00');
denied('2026-02-30','2026-02-30 08:00:00');
denied('2026-12-01','2026-12-01T08:00:00');
denied('2026-12-01','2026-12-01 24:00:00x');
denied('2026-12-01','2026-12-01');
$reader=file_get_contents(__DIR__.'/../../private/booking-sidecar/BookingWriteProofReader.php');
$rows=file_get_contents(__DIR__.'/../../private/booking-sidecar/BookingWriteProofRows.php');
if(!str_contains($reader,'client_id,order_date,booking_date_time')||!str_contains($rows,'BookingWriteProofOrderDate::check'))throw new RuntimeException('Proof reader/writer wiring missing');
$checks++;
echo "PASS117: {$checks} assertions passed\n";
