<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass47/bootstrap.php';
function check49(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function reject49(callable $fn,string $message):void {try{$fn();}catch(Throwable $e){check49(str_contains($e->getMessage(),$message),'Unexpected error: '.$e->getMessage());return;}throw new RuntimeException('Expected rejection: '.$message);}
class Pass49Statement extends Pass47Statement {}
class Pass49Database extends Pass47PDO {
 public bool $failQuery=false;public mixed $database='tenant_a';
 public function query(string $query,?int $fetchMode=null,mixed ...$fetchModeArgs):PDOStatement|false {
  if($this->failQuery)return false;
  $s=new Pass49Statement;$s->column=$this->database;return $s;
 }
}
