<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{Interval,ConflictPayload,BookingConflictReader};
$count=0;
function ok(bool $value, string $label): void {global $count;if (!$value) throw new RuntimeException('FAIL '.$label);$count++;echo 'PASS '.$label."\n";}
function rejected(callable $fn, string $label): void {try {$fn();throw new RuntimeException('FAIL '.$label);} catch (InvalidArgumentException $e) {ok(true,$label);}}
ok(Interval::overlaps(10,20,19,30),'overlap detected');
ok(!Interval::overlaps(10,20,20,30),'end touching start allowed');
ok(!Interval::overlaps(20,30,10,20),'start touching end allowed');
ok(Interval::overlaps(10,100,20,30),'containing interval overlaps');
rejected(fn()=>Interval::overlaps(10,10,10,20),'zero length rejected');
rejected(fn()=>Interval::parse('2026-02-30 09:00:00'),'invalid calendar rejected');
rejected(fn()=>Interval::parse('2026-11-10 09:00:00','Invalid/Zone'),'unknown timezone rejected');
$good=['staff_id'=>3,'from'=>'2026-11-10 09:00:00','until'=>'2026-11-10 17:00:00','timezone'=>'Australia/Melbourne'];
ok(ConflictPayload::parse($good)['staff_id']===3,'valid staff lookup');
rejected(fn()=>ConflictPayload::parse(array_merge($good,['staff_id'=>0])),'staff zero rejected');
rejected(fn()=>ConflictPayload::parse(array_merge($good,['until'=>'2026-11-19 17:00:00'])),'oversized range rejected');
rejected(fn()=>ConflictPayload::parse(array_merge($good,['until'=>'2026-11-10 08:59:00'])),'backwards range rejected');
ok(class_exists(BookingConflictReader::class),'legacy schema reader autoloads');
echo "TOTAL PASS $count\n";
