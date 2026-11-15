<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Immutable-like interval transformations with half-open [start,end) semantics. */
final class IntervalSet {
 public static function merge(array $intervals): array {
  foreach ($intervals as $i) if (count($i)!==2 || !is_int($i[0]) || !is_int($i[1]) || $i[1]<=$i[0]) throw new InvalidArgumentException('Bad interval');
  usort($intervals,static fn($a,$b)=>$a[0]<=>$b[0]);$result=[];
  foreach ($intervals as $i) {
   $k=count($result)-1;
   if ($k>=0 && $i[0]<=$result[$k][1]) $result[$k][1]=max($result[$k][1],$i[1]);
   else $result[]=$i;
  }
  return $result;
 }
 public static function subtract(array $working,array $blocked): array {
  $working=self::merge($working);$blocked=self::merge($blocked);$out=[];
  foreach ($working as [$a,$b]) {
   $cursor=$a;
   foreach ($blocked as [$x,$y]) {
    if ($y<=$cursor || $x>=$b) continue;
    if ($x>$cursor) $out[]=[$cursor,min($x,$b)];
    $cursor=max($cursor,min($y,$b));if ($cursor>=$b) break;
   }
   if ($cursor<$b) $out[]=[$cursor,$b];
  }
  return $out;
 }
}
