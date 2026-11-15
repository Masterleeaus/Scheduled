<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewResult {
 public static function present(array $request,array $quote,array $slots):array {
  return ['date'=>$request['date'],'timezone'=>$request['timezone'],'staff_ids'=>$request['staff_ids'],'duration_minutes'=>$request['duration_minutes'],'service'=>$request['service'],'quote'=>$quote,'available_starts'=>$slots,'reservable'=>false,'status'=>'preview_only','requires_final_availability_recheck'=>true];
 }
}
