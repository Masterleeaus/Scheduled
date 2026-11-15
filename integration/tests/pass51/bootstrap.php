<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass47/bootstrap.php';
function p51(bool $ok,string $why):void {if(!$ok)throw new RuntimeException($why);}
function rejects51(callable $fn,string $part):void {try {$fn();}catch(Throwable $e){p51(str_contains($e->getMessage(),$part),'Unexpected error: '.$e->getMessage());return;}throw new RuntimeException('Expected '.$part);}
class Row51 extends Pass47Statement {
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT,mixed ...$args):array|false {$out=$this->data;$this->data=[];return $out;}
}
class Db51 extends Pass47PDO {}
function db51(array $rows=[],bool $ok=true,int $count=1,bool $active=true):Db51 {
 $db=new Db51;$db->active=$active;$s=new Row51;$s->data=$rows;$s->ok=$ok;$s->rows=$count;$db->queue=[$s];return $db;
}
function row51(int $id=7):array{return ['id'=>$id,'order_id'=>11,'booking_status'=>'A','booking_date_time'=>'2026-11-12 10:00:00','staff_ids'=>'2'];}
function key51():string {return str_repeat('a',24);}
function hash51():string{return str_repeat('b',64);}
