<?php
declare(strict_types=1);
require __DIR__.'/../core/app/Booking/Public/Redemption/CommitTransactionBoundary.php';
require __DIR__.'/../core/app/Booking/Public/Redemption/CommitLegacyEvidenceReader.php';
use App\Booking\Public\Redemption\CommitLegacyEvidenceReader as Reader;
class Statement86 extends PDOStatement {
 public bool $executeResult=true; public array $rows=[];
 public function __construct(){}
 public function execute(?array $params=null):bool {return $this->executeResult;}
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT,...$args):array|false{return $this->rows;}
}
class Database86 extends PDO {
 public bool $prepared=true; public bool $transaction=true; public Statement86 $statement;
 public function __construct(){$this->statement=new Statement86();}
 public function inTransaction():bool{return $this->transaction;}
 public function prepare(string $query,array $options=[]):PDOStatement|false {return $this->prepared?$this->statement:false;}
}
function expectsFailure86(callable $fn,string $message):void {
 try{$fn();}catch(Throwable $e){if($e->getMessage()===$message)return;throw new RuntimeException('Unexpected error: '.$e->getMessage());}
 throw new RuntimeException('Did not fail: '.$message);
}
$db=new Database86();
expectsFailure86(fn()=>Reader::read($db,0),'Invalid booking');
$db->transaction=false;expectsFailure86(fn()=>Reader::read($db,1),'Booking write outside transaction');
$db->transaction=true;$db->prepared=false;
expectsFailure86(fn()=>Reader::read($db,1),'Booking evidence query failed');
$db->prepared=true;$db->statement->executeResult=false;
expectsFailure86(fn()=>Reader::read($db,1),'Booking evidence query failed');
$db->statement->executeResult=true;$db->statement->rows=[];
expectsFailure86(fn()=>Reader::read($db,1),'Expected exactly one persisted booking');
$db->statement->rows=[['id'=>1],['id'=>1]];
expectsFailure86(fn()=>Reader::read($db,1),'Expected exactly one persisted booking');
$db->statement->rows=[['id'=>1,'order_id'=>2]];
if(Reader::read($db,1)['order_id']!==2)throw new RuntimeException('Valid evidence rejected');
echo "PASS: 7 PDO booking readback cases\n";
