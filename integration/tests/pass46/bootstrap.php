<?php
declare(strict_types=1);
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingTransaction.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingSharedLockLease.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingLockManager.php';
class Pass46PDO extends PDO {
 public bool $active=false,$beginOk=true,$commitOk=true,$rollbackOk=true,$beginThrows=false,$commitThrows=false,$rollbackThrows=false;
 public int $begins=0,$commits=0,$rollbacks=0;
 public function __construct(){}
 public function inTransaction():bool{return $this->active;}
 public function beginTransaction():bool{++$this->begins;if($this->beginThrows)throw new RuntimeException('begin exception');if($this->beginOk)$this->active=true;return $this->beginOk;}
 public function commit():bool{++$this->commits;if($this->commitThrows)throw new RuntimeException('commit exception');if($this->commitOk)$this->active=false;return $this->commitOk;}
 public function rollBack():bool{++$this->rollbacks;if($this->rollbackThrows)throw new RuntimeException('rollback exception');if($this->rollbackOk)$this->active=false;return $this->rollbackOk;}
}
function check46(bool $ok,string $reason):void{if(!$ok)throw new RuntimeException($reason);}
function reject46(callable $action,string $message=''):void{try{$action();}catch(Throwable $e){if($message!==''&&!str_contains($e->getMessage(),$message))throw new RuntimeException('Unexpected failure: '.$e->getMessage());return;}throw new RuntimeException('Expected rejection');}
