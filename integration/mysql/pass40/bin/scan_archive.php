<?php
declare(strict_types=1);
/** Inspect distribution without executing legacy Cleanto PHP. */
final class Pass40Archive {
 public static function check(array $paths):array {
  $required=['private/cleanto/','core/app/Booking/Public/Redemption/CommitOrchestrator.php','integration/mysql/lifecycle/run_lifecycle.php'];
  $missing=[];foreach($required as $r){$found=false;foreach($paths as $p){if(str_starts_with($p,$r)){$found=true;break;}}if(!$found)$missing[]=$r;}
  return ['complete'=>count($missing)===0,'missing'=>$missing];
 }
}
