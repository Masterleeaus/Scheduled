<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;use RuntimeException;
/** MySQL connection-level named locks: acquired before transactions, always released. */
final class BookingLockManager {
 private array $held=[];
 public function __construct(private PDO $pdo) {}
 public function acquire(array $keys,int $timeout=5,?int $absoluteDeadlineNs=null):void {
  if($this->pdo->inTransaction())throw new \LogicException('Advisory locks must be acquired before a transaction');
  if($timeout<0||$timeout>30)throw new \InvalidArgumentException('Invalid lock timeout');
  if(!$keys)throw new \InvalidArgumentException('Booking lock set cannot be empty');
  // Validate before sorting or issuing SQL: PHP's generic sort can coerce mixed types.
  foreach($keys as $key){
   if(!is_string($key)||!preg_match('/^tb:(?:staff|booking):[0-9]{1,12}$|^tb:[a-f0-9]{12}:(?:staff|booking):[0-9]{1,12}$/D',$key))throw new \InvalidArgumentException('Invalid lock key');
  }
  $keys=array_values(array_unique($keys));sort($keys,SORT_STRING);
  // One budget for the whole lock set. Per-lock timeout multiplication can stall
  // booking requests for minutes when a service needs several staff members.
  $defaultDeadline=hrtime(true)+$timeout*1000000000;
  if($absoluteDeadlineNs!==null && $absoluteDeadlineNs<=0)throw new \InvalidArgumentException('Invalid absolute lock deadline');
  // A caller may shorten, but never extend, the local acquisition budget.
  $deadline=$absoluteDeadlineNs===null?$defaultDeadline:min($defaultDeadline,$absoluteDeadlineNs);
  try {
   foreach($keys as $key){
    if(in_array($key,$this->held,true))continue;
    $remaining=max(0.0,($deadline-hrtime(true))/1000000000);
    // MariaDB/MySQL GET_LOCK supports fractional-second timeouts. Keep the
    // fraction so late keys cannot restart a fresh multi-second wait.
    $wait=round($remaining,3);
    if($wait<=0.0 && ($timeout!==0 || $absoluteDeadlineNs!==null))throw new RuntimeException('Booking lock acquisition deadline exceeded');
    $stmt=$this->pdo->prepare('SELECT GET_LOCK(?, ?)');
    if($stmt===false||$stmt->execute([$key,$wait])!==true)throw new RuntimeException('Booking lock query failed');
    if(!in_array($stmt->fetchColumn(),[1,'1'],true))throw new RuntimeException('Booking lock unavailable');
    $this->held[]=$key;
   }
  } catch (\Throwable $error) {
   try{$this->releaseAll();}catch(\Throwable $cleanup){throw new \RuntimeException('Booking lock cleanup failed',0,$error);}
   throw $error;
  }
 }
 public function releaseAll():void {
  if($this->pdo->inTransaction())throw new \LogicException('Advisory locks must be released after transaction completion');
  $errors=[];$unreleased=[];
  foreach(array_reverse($this->held) as $key){
   try{
    $stmt=$this->pdo->prepare('SELECT RELEASE_LOCK(?)');
    if($stmt===false||$stmt->execute([$key])!==true)throw new RuntimeException('Booking unlock query failed');
    if(!in_array($stmt->fetchColumn(),[1,'1'],true))throw new RuntimeException('Booking lock release failed');
   }catch(\Throwable $e){$errors[]=$e;$unreleased[]=$key;}
  }
  $this->held=array_reverse($unreleased);
  if($errors)throw new RuntimeException('One or more booking locks could not be released',0,$errors[0]);
 }
 public function held():array {return $this->held;}
}
