<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Verify a complete lock plan belongs to the currently selected tenant database. */
final class BookingLockPlanAudit {
 public static function inspect(string $namespace,array $keys):array {
  $ns=BookingLockNamespace::validate($namespace);$seen=[];
  foreach($keys as $key){
   if(!is_string($key)||!preg_match('/^tb:'.preg_quote($ns,'/').':(?:staff|booking):[0-9]{1,12}$/D',$key))throw new \DomainException('Foreign or malformed booking lock');
   $seen[$key]=true;
  }
  $canonical=array_keys($seen);sort($canonical,SORT_STRING);
  return ['keys'=>$canonical,'duplicates'=>count($keys)-count($canonical)];
 }
}
