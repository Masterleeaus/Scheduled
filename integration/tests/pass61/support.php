<?php
declare(strict_types=1);
require_once dirname(__DIR__,2).'/mysql/lifecycle/bootstrap.php';
class Pass61Statement extends PDOStatement {
 public function __construct(public array|false $rows=[],public bool $executed=true){}
 public function execute(?array $params=null):bool{return $this->executed;}
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT,...$args):array|false{return $this->rows;}
}
class Pass61PDO extends PDO {
 public string $lastSql=''; public array $params=[];
 public function __construct(public array|false $rows=[],public bool $prepared=true,public bool $executed=true){}
 public function prepare(string $query,array $options=[]):PDOStatement|false { $this->lastSql=$query;return $this->prepared?new Pass61Statement($this->rows,$this->executed):false;}
}
function p61_reject(callable $call,string $name):void {try{$call();}catch(Throwable $e){return;}throw new RuntimeException('Expected rejection: '.$name);}
function p61_assert(bool $condition,string $label):void {if(!$condition)throw new RuntimeException($label);}
