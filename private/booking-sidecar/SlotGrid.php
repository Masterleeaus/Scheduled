<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use DateTimeImmutable;
/** Generate candidate slots within free intervals, aligning to wall-clock grid. */
final class SlotGrid {
 public static function generate(array $free,DateTimeImmutable $midnight,int $duration,int $step): array {
  if ($duration<15 || $duration>720 || $step<5 || $step>120) throw new \InvalidArgumentException('Invalid slot settings');
  $result=[];$anchor=$midnight->getTimestamp();
  foreach ($free as [$a,$b]) {
   $start=$anchor+(int)ceil(($a-$anchor)/($step*60))*$step*60;
   while ($start+$duration*60<=$b) {
    $result[]=date('c',$start);if (count($result)>500) throw new \RuntimeException('Slot output too large');
    $start+=$step*60;
   }
  }
  return $result;
 }
}
