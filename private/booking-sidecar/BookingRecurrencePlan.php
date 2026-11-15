<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use DateTimeImmutable;use DateTimeZone;
/** Produces candidate local dates only; no claim of reserved slots. */
final class BookingRecurrencePlan {
 public static function dates(string $localStart,string $zone,array $rule):array {
  $start=Interval::parse($localStart,$zone);$r=BookingRecurrenceRule::parse($rule);$out=[];
  for($i=0;$i<$r['count'];$i++){
   $candidate=match($r['frequency']){'weekly'=>$start->modify('+'.($i*7).' days'),'fortnightly'=>$start->modify('+'.($i*14).' days'),'monthly'=>$start->modify('+'.$i.' months')};
   if($r['frequency']==='monthly'&&$candidate->format('j')!==$start->format('j'))break;
   $out[]=$candidate->format('Y-m-d H:i:s');
  }
  return $out;
 }
}
