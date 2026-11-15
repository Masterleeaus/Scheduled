<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass47/bootstrap.php';
class P48Statement extends Pass47Statement {
 public array|false $records=[];
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT, mixed ...$args):array|false {return $this->records;}
}
function p48(bool $ok,string $msg):void {if(!$ok)throw new RuntimeException($msg);}
function rejects48(callable $task,string $expected):void {try{$task();}catch(Throwable $e){p48(str_contains($e->getMessage(),$expected),'Unexpected: '.$e->getMessage());return;}throw new RuntimeException('Expected rejection: '.$expected);}
