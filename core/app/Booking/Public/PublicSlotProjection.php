<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicSlotProjection {
 public static function fromSidecar(array $result,array $request):array {
  $slots=$result['slots']??null;if(!is_array($slots)||!array_is_list($slots)||count($slots)>500)throw new \RuntimeException('Invalid availability response');
  $out=[];$seen=[];
  foreach($slots as $candidate){$iso=PublicSlotFormat::normalize($candidate,$request['timezone']);$local=(new \DateTimeImmutable($iso))->setTimezone(new \DateTimeZone($request['timezone']));
   if($local->format('Y-m-d')!==$request['date'])throw new \RuntimeException('Slot outside requested day');
   if(isset($seen[$iso]))throw new \RuntimeException('Duplicate appointment slot');$seen[$iso]=true;$out[]=$iso;
  }
  if(count($out)>120)throw new \RuntimeException('Too many public slots');sort($out,SORT_STRING);
  return ['available_starts'=>$out,'preview_only'=>true,'booking_writes_enabled'=>false,'appointment_created'=>false];
 }
}
