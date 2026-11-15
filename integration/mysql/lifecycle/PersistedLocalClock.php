<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Converts a legacy wall-clock timestamp only when its UTC instant is unambiguous. */
final class PersistedLocalClock {
 public static function instant(string $local,string $zone):int {
  if(!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D',$local))throw new \DomainException('Invalid local booking timestamp');
  try{$tz=new \DateTimeZone($zone);}catch(\Throwable $e){throw new \DomainException('Invalid booking timezone',0,$e);}
  $dt=\DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$local,$tz);
  if(!$dt||$dt->format('Y-m-d H:i:s')!==$local)throw new \DomainException('Nonexistent booking local time');
  $timestamp=$dt->getTimestamp();
  $transitions=$tz->getTransitions($timestamp-172800,$timestamp+172800);
  // Fixed-offset timezone names may have no transition table.
  if($transitions===false)return $timestamp;
  $offsets=[];foreach($transitions as $tr)$offsets[(string)$tr['offset']]=$tr['offset'];
  $localEpoch=\DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$local,new \DateTimeZone('UTC'))->getTimestamp();
  $valid=[];foreach($offsets as $offset){$candidate=$localEpoch-$offset;
   if((new \DateTimeImmutable('@'.$candidate))->setTimezone($tz)->format('Y-m-d H:i:s')===$local)$valid[$candidate]=true;
  }
  if(count($valid)!==1)throw new \DomainException('Ambiguous or nonexistent booking local time');
  return (int)array_key_first($valid);
 }
}
