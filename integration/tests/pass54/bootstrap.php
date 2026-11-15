<?php
declare(strict_types=1);
require_once dirname(__DIR__,2).'/mysql/bootstrap.php';
use Titan\BookingIntegration\CheckedProbeSql;
class P54Statement extends PDOStatement {
 public mixed $column=1;
 public mixed $all=[];
 public bool $executed=true;
 public function __construct(){}
 public function execute(?array $params=null):bool {return $this->executed;}
 public function fetchColumn(int $column=0):mixed{return $this->column;}
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT,mixed ...$args):array|false{return $this->all;}
}
class P54Pdo extends PDO {
 public bool $queryFail=false,$prepareFail=false,$transaction=false,$startOk=true,$rollbackOk=true,$emulation=false;
 public P54Statement $stmt;
 public function __construct(){ $this->stmt=new P54Statement(); }
 public function query(string $query,?int $fetchMode=null,mixed ...$fetchModeArgs):PDOStatement|false{return $this->queryFail?false:$this->stmt;}
 public function prepare(string $query,array $options=[]):PDOStatement|false{return $this->prepareFail?false:$this->stmt;}
 public function inTransaction():bool{return $this->transaction;}
 public function beginTransaction():bool {if($this->startOk)$this->transaction=true;return $this->startOk;}
 public function rollBack():bool {if($this->rollbackOk)$this->transaction=false;return $this->rollbackOk;}
 public function getAttribute(int $attribute):mixed{return $attribute===PDO::ATTR_EMULATE_PREPARES?$this->emulation:false;}
}
function check54(callable $fn,bool $reject,string $message):void {
 $thrown=false;try{$fn();}catch(Throwable $e){$thrown=true;}
 if($thrown!==$reject)throw new RuntimeException('Pass54 contract failed: '.$message);
}
