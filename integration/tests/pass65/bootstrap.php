<?php
declare(strict_types=1);
require_once dirname(__DIR__,3).'/private/booking-sidecar/BookingLockManager.php';
class P65Statement extends PDOStatement {
 public mixed $scalar=1;public bool $executeOk=true;public array $params=[];public int $delayUs=0;
 public function __construct(){}
 public function execute(?array $params=null):bool { $this->params=$params??[];if($this->delayUs>0)usleep($this->delayUs);return $this->executeOk; }
 public function fetchColumn(int $column=0):mixed {return $this->scalar;}
}
class P65PDO extends PDO {
 public bool $active=false;public bool $failPrepare=false;public array $queue=[],$sql=[],$statements=[];
 public function __construct(){}
 public function inTransaction():bool {return $this->active;}
 public function prepare(string $query,array $options=[]):PDOStatement|false {
  $this->sql[]=$query;if($this->failPrepare)return false;
  $s=array_shift($this->queue)??new P65Statement();$this->statements[]=$s;return $s;
 }
}
function p65(bool $check,string $reason):void {if(!$check)throw new RuntimeException($reason);}
function reject65(callable $callback,string $text):void {
 try{$callback();}catch(Throwable $e){if(!str_contains($e->getMessage(),$text))throw new RuntimeException('Unexpected error: '.$e->getMessage());return;}
 throw new RuntimeException('Expected rejection: '.$text);
}
