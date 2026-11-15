<?php
declare(strict_types=1);
require_once dirname(__DIR__,2).'/mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\LifecycleFootprintSql;
class P62Statement extends PDOStatement {
 public function __construct(public array|false $rows=[['total'=>'1']],public bool $ok=true){}
 public function execute(?array $params=null):bool{return $this->ok;}
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT,...$args):array|false{return $this->rows;}
}
class P62PDO extends PDO {
 public array $sql=[];
 public function __construct(public array|false $rows=[['total'=>'1']],public bool $prepared=true,public bool $ok=true,public bool $inside=false){}
 public function prepare(string $query,array $options=[]):PDOStatement|false {
  $this->sql[]=$query;return $this->prepared?new P62Statement($this->rows,$this->ok):false;
 }
 public function inTransaction():bool{return $this->inside;}
}
function p62_ok(bool $value,string $message):void {if(!$value)throw new RuntimeException($message);}
function p62_fail(callable $fn,string $message):void {
 try{$fn();}catch(Throwable $e){return;}
 throw new RuntimeException('Expected failure: '.$message);
}
