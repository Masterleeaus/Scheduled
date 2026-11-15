<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void {
 $a='Titan\\BookingSidecar\\'; $b='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($class,$a)){$p=dirname(__DIR__,3).'/private/booking-sidecar/'.substr($class,strlen($a)).'.php';if(is_file($p))require_once $p;}
 if(str_starts_with($class,$b)){$p=dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/'.substr($class,strlen($b)).'.php';if(is_file($p))require_once $p;}
});
class Pass47Statement extends PDOStatement {
 public bool $ok=true;public int $rows=1;public array $data=[];public mixed $column=false;
 public array $input=[]; public int $runs=0;
 public function __construct(){}
 public function execute(?array $params=null):bool{$this->input=$params??[];$this->runs++;return $this->ok;}
 public function rowCount():int{return $this->rows;}
 public function fetch(int $mode=PDO::FETCH_DEFAULT,int $cursorOrientation=PDO::FETCH_ORI_NEXT,int $cursorOffset=0):mixed{return array_shift($this->data)??false;}
 public function fetchColumn(int $column=0):mixed{return $this->column;}
}
class Pass47PDO extends PDO {
 public array $queue=[];public bool $failPrepare=false,$active=true;public int|false $execValue=1;public string|false $insertId='27';public array $queries=[];
 public function __construct(){}
 public function prepare(string $query,array $options=[]):PDOStatement|false{$this->queries[]=$query;if($this->failPrepare)return false;return array_shift($this->queue)??new Pass47Statement;}
 public function exec(string $statement):int|false{$this->queries[]=$statement;return $this->execValue;}
 public function lastInsertId(?string $name=null):string|false{return $this->insertId;}
 public function inTransaction():bool{return $this->active;}
}
function p47(bool $yes,string $message):void{if(!$yes)throw new RuntimeException($message);}
function fails47(callable $call,string $snippet):void{try{$call();}catch(Throwable $e){p47(str_contains($e->getMessage(),$snippet),'Wrong failure: '.$e->getMessage());return;}throw new RuntimeException('Expected failure: '.$snippet);}
function customer47():array{return ['name'=>'Test Customer','email'=>'test@example.com','phone'=>'0412345678'];}
function command47():array{return ['service'=>['service_id'=>1,'method_id'=>1,'method_unit_id'=>1,'quantity'=>1],'customer'=>customer47(),'staff_ids'=>[2],'duration_minutes'=>60,'window'=>['local_start'=>'2026-11-12 10:00:00']];}
