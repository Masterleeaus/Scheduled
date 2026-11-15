<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Avoid lossy integer coercion of large, malformed or zero staff identifiers. */
final class BookingStrictStaffIds {
 public static function parse(string $stored):array {
  if(!preg_match('/^[1-9][0-9]*(,[1-9][0-9]*)*$/D',$stored))throw new \DomainException('Invalid stored staff');
  $out=[];
  foreach(explode(',',$stored) as $part){
   if(strlen($part)>strlen((string)PHP_INT_MAX)||(strlen($part)===strlen((string)PHP_INT_MAX)&&strcmp($part,(string)PHP_INT_MAX)>0))throw new \DomainException('Staff identifier overflow');
   $id=(int)$part;
   if($id<1||in_array($id,$out,true))throw new \DomainException('Duplicate or invalid stored staff');
   $out[]=$id;
  }
  return $out;
 }
}
