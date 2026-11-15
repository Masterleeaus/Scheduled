<?php
declare(strict_types=1);
require_once dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/RedeemTransaction.php';
require_once dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitTransactionBoundary.php';
require_once dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitBookingId.php';
require_once dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/CommitCallback.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingAtomicPersistBoundary.php';
class Pass45PDO extends PDO {
 public bool $active=false;
 public bool $beginOk=true;
 public bool $commitOk=true;
 public bool $rollbackOk=true;
 public bool $closeBeforeCommit=false;
 public bool $throwBegin=false;
 public bool $throwCommit=false;
 public bool $throwRollback=false;
 public int $begins=0;
 public int $commits=0;
 public int $rollbacks=0;
 public function __construct(){}
 public function inTransaction():bool{return $this->active;}
 public function beginTransaction():bool{++$this->begins;if($this->throwBegin)throw new RuntimeException('begin threw');if($this->beginOk)$this->active=true;return $this->beginOk;}
 public function commit():bool{++$this->commits;if($this->throwCommit)throw new RuntimeException('commit threw');if($this->closeBeforeCommit)$this->active=false;if($this->commitOk)$this->active=false;return $this->commitOk;}
 public function rollBack():bool{++$this->rollbacks;if($this->throwRollback)throw new RuntimeException('rollback threw');if($this->rollbackOk)$this->active=false;return $this->rollbackOk;}
}
function pass45_assert(bool $condition,string $description):void {if(!$condition)throw new RuntimeException($description);}
function pass45_throws(callable $fn,string $text=''):void {try{$fn();}catch(Throwable $e){if($text!==''&&!str_contains($e->getMessage(),$text))throw new RuntimeException('Wrong error: '.$e->getMessage());return;}throw new RuntimeException('Expected rejection');}
