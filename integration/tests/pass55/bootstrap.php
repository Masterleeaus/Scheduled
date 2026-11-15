<?php
declare(strict_types=1);
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingLockManager.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingLockKeys.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingStaff.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingLockNamespace.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingSharedLockLease.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingSharedStaffLocks.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingTenantMutationLocks.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingAtomicLockLease.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingDatabaseIdentity.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingTenantDatabase.php';
class Pass55Stmt extends PDOStatement {
 public mixed $scalar=1; public bool $executeOk=true; public array $params=[];
 public function __construct(){}
 public function execute(?array $params=null):bool{$this->params=$params??[];return $this->executeOk;}
 public function fetchColumn(int $column=0):mixed{return $this->scalar;}
}
class Pass55PDO extends PDO {
 public bool $active=false;public array $queue=[],$queries=[];public bool $prepareFalse=false;
 public function __construct(){}
 public function inTransaction():bool{return $this->active;}
 public function prepare(string $query,array $options=[]):PDOStatement|false {
  $this->queries[]=$query;
  if($this->prepareFalse)return false;
  return array_shift($this->queue)??new Pass55Stmt();
 }
}
function a55(bool $condition,string $label):void{if(!$condition)throw new RuntimeException($label);}
function reject55(callable $fn,string $part=''):void{
 try{$fn();}catch(Throwable $e){if($part!==''&&!str_contains($e->getMessage(),$part))throw new RuntimeException('Wrong rejection: '.$e->getMessage());return;}
 throw new RuntimeException('Expected rejection');
}
