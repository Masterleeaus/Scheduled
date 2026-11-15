<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass44/bootstrap.php';
use Titan\BookingSidecar\BookingCreationReplayProof;
class Pass53Statement extends PDOStatement {
 public array $rows=[]; public bool $executeOk=true; public function __construct(array $rows=[],bool $ok=true){$this->rows=$rows;$this->executeOk=$ok;}
 public function execute(?array $params=null):bool{return $this->executeOk;}
 public function fetch(int $mode=PDO::FETCH_DEFAULT,int $cursorOrientation=PDO::FETCH_ORI_NEXT,int $cursorOffset=0):mixed{return array_shift($this->rows)??false;}
}
class Pass53PDO extends PDO {
 public array $statements=[]; public bool $active=true; public bool $failPrepare=false;
 public function __construct(array $statements=[]){$this->statements=$statements;}
 public function inTransaction():bool{return $this->active;}
 public function prepare(string $query,array $options=[]):PDOStatement|false {if($this->failPrepare)return false;return array_shift($this->statements)??false;}
}
function db53(?array $booking=null,?array $order=null):Pass53PDO {
 return new Pass53PDO([new Pass53Statement([$booking??pass44_booking()]),new Pass53Statement([$order??pass44_order()])]);
}
function assert53(bool $v,string $why):void {if(!$v)throw new RuntimeException($why);}
function reject53(callable $cb,string $message):void {try{$cb();}catch(Throwable $e){assert53(str_contains($e->getMessage(),$message),'Expected '. $message .' but got '.get_class($e).': '.$e->getMessage());return;}throw new RuntimeException('Expected rejection: '.$message);}
function prove53(?array $booking=null,?array $order=null,?array $command=null,?Pass53PDO $db=null):void {
 BookingCreationReplayProof::verify($db??db53($booking,$order),17,$command??pass44_command());
}
