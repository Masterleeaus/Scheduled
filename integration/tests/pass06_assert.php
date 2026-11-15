<?php
declare(strict_types=1);
/** Isolated PDO double: verifies SQL contracts without claiming database integration. */
function a(bool $condition,string $message):void {if(!$condition)throw new RuntimeException($message);}
function fails(callable $f,string $message):void {$failed=false;try{$f();}catch(Throwable $e){$failed=true;}a($failed,$message);}
class FakeStatement extends PDOStatement {
 public function __construct(private FakePDO $db,private string $sql){}
 public function execute(?array $params=null):bool {$this->db->calls[]=[$this->sql,$params??[]];return true;}
 public function fetchColumn(int $column=0):mixed {foreach($this->db->scalarBySql as $part=>$value)if(str_contains($this->sql,$part))return $value;return false;}
 public function fetch(int $mode=PDO::FETCH_DEFAULT,int $cursorOrientation=PDO::FETCH_ORI_NEXT,int $cursorOffset=0):mixed {$row=$this->db->fetchRow;$this->db->fetchRow=false;return $row;}
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT,...$args):array{return $this->db->fetchRows;}
 public function rowCount():int{return $this->db->affected;}
}
class FakePDO extends PDO {
 public array $calls=[];public array $scalarBySql=[];public array|false $fetchRow=false;public array $fetchRows=[];public int $affected=1;public string $lastId='1';
 public int $commitCalls=0;public int $rollbackCalls=0;private bool $inside=false;
 public function __construct(){}
 public function prepare(string $query,array $options=[]):PDOStatement|false {return new FakeStatement($this,$query);}
 public function exec(string $statement):int|false {$this->calls[]=[$statement,[]];return 1;}
 public function lastInsertId(?string $name=null):string|false{return $this->lastId;}
 public function beginTransaction():bool{$this->inside=true;return true;}
 public function commit():bool{$this->inside=false;$this->commitCalls++;return true;}
 public function rollBack():bool{$this->inside=false;$this->rollbackCalls++;return true;}
 public function inTransaction():bool{return $this->inside;}
}
function sampleBooking():array {
 return Titan\BookingSidecar\BookingCommand::create(['idempotency_key'=>str_repeat('k',24),'staff_ids'=>[5,2],
  'service'=>['service_id'=>2,'method_id'=>3,'method_unit_id'=>4,'quantity'=>1],
  'customer'=>['name'=>'Jane Test','email'=>'jane@example.test','phone'=>'+61412345678'],
  'start_at'=>'2026-11-10 09:00:00','duration_minutes'=>90,'timezone'=>'Australia/Melbourne']);
}
