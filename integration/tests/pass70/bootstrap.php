<?php
declare(strict_types=1);
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingLockManager.php';
class P70Statement extends PDOStatement {
 public mixed $scalar=1;public bool $executeOk=true;public array $params=[];public int $delayUs=0;
 public function __construct(){}
 public function execute(?array $params=null):bool {$this->params=$params??[];if($this->delayUs)usleep($this->delayUs);return $this->executeOk;}
 public function fetchColumn(int $column=0):mixed {return $this->scalar;}
}
class P70PDO extends PDO {
 public bool $active=false;public array $queue=[],$statements=[];
 public function __construct(){}
 public function inTransaction():bool {return $this->active;}
 public function prepare(string $query,array $options=[]):PDOStatement|false {
  $s=array_shift($this->queue)??new P70Statement();$this->statements[]=$s;return $s;
 }
}
function p70(bool $ok,string $why):void {if(!$ok)throw new RuntimeException($why);}
function reject70(callable $fn,string $part):void {try{$fn();}catch(Throwable $e){if(str_contains($e->getMessage(),$part))return;throw $e;}throw new RuntimeException('Missing expected rejection '.$part);}
