<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use DomainException;
/** Match Cleanto's existing booking_status enum. */
final class BookingStateMachine {
 private const MOVES=['A'=>['C','R','CC','CS','MN'],'C'=>['RS','CC','CS','CO','MN'],'RS'=>['C','CC','CS'],'R'=>[],'CC'=>[],'CS'=>[],'CO'=>[],'MN'=>[]];
 public static function transition(string $current,string $next):string {
  if(!in_array($next,self::MOVES[$current]??[],true))throw new DomainException('Booking status transition forbidden');
  return $next;
 }
 public static function occupiesStaff(string $status):bool {return in_array($status,['A','C','RS'],true);}
}
