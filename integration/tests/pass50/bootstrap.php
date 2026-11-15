<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass47/bootstrap.php';
function ok50(bool $yes,string $reason):void {if(!$yes)throw new RuntimeException($reason);}
function fail50(callable $f,string $needle):void {try{$f();}catch(Throwable $e){ok50(str_contains($e->getMessage(),$needle),'Expected '.$needle.' got '.$e->getMessage());return;}throw new RuntimeException('Did not reject '.$needle);}
class Pass50Statement extends Pass47Statement {}
class Pass50PDO extends Pass47PDO {}
function row50():array{return ['id'=>27,'order_id'=>31,'client_id'=>32,'booking_date_time'=>'2026-11-12 10:00:00','booking_status'=>'A','staff_ids'=>'2'];}
function reader50(array $rows, bool $execute=true, bool $transaction=true, bool $prepare=true):Pass50PDO {
 $db=new Pass50PDO;$db->active=$transaction;$db->failPrepare=!$prepare;$s=new Pass50Statement;$s->data=$rows;$s->ok=$execute;$db->queue=[$s];return $db;
}
