<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Report release state without pretending unit-only verification proves MySQL behavior. */
final class BookingLockReleaseReadiness {
 public static function check(array $tests):array {
  $required=['partial_acquisition_cleanup','release_error_retention','mysql_named_lock_release','tenant_namespace_separation'];
  $missing=[];foreach($required as $key)if(($tests[$key]??false)!==true)$missing[]=$key;
  return ['ready'=>$missing===[],'missing'=>$missing,'mysql_tested'=>($tests['mysql_named_lock_release']??false)===true];
 }
}
