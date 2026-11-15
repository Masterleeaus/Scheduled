<?php
namespace Tests\Unit;
use PHPUnit\Framework\TestCase;
require_once __DIR__.'/../../../../private/booking-sidecar/BookingOwnerFilters.php';
final class BookingOwnerOperationalFiltersTest extends TestCase {
 public function testBoundsAndDates():void {
  $this->assertSame(['from'=>'2026-11-01','to'=>'2026-11-30','staff_id'=>11],\Titan\BookingSidecar\BookingOwnerFilters::from(['from'=>'2026-11-01','to'=>'2026-11-30','staff_id'=>'11']));
  $this->expectException(\InvalidArgumentException::class);\Titan\BookingSidecar\BookingOwnerFilters::from(['from'=>'2026-02-30']);
 }
 public function testRangeOrder():void {$this->expectException(\InvalidArgumentException::class);\Titan\BookingSidecar\BookingOwnerFilters::from(['from'=>'2026-12-01','to'=>'2026-11-01']);}
 public function testWhereUsesParameters():void {$p=[];$sql=\Titan\BookingSidecar\BookingOwnerFilters::where(['staff_id'=>11,'status'=>'A'],$p);$this->assertStringContainsString('FIND_IN_SET', $sql);$this->assertSame(['A','11'],$p);}
}
