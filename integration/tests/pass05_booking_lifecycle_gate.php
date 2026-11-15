<?php
declare(strict_types=1);
// S5: booking_cancel and booking_reschedule gate, submission safety and cancel lock/requery flow.
// Uses an in-process PDO double. No database is contacted; live MySQL proof stays in integration/mysql-tests.
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{Dispatcher,BookingCancellationInput,BookingMutationFingerprint};

function s5_check(bool $condition,string $message):void {if(!$condition)throw new RuntimeException($message);}
function s5_throws(callable $callback,string $class,string $message):void {
 try{$callback();}catch(Throwable $e){
  if($e instanceof $class)return;
  throw new RuntimeException($message.' (got '.get_class($e).': '.$e->getMessage().')');
 }
 throw new RuntimeException($message.' (no exception)');
}

final class S5Statement extends PDOStatement {
 private array $pending=[];
 private int $rows=0;
 public function __construct(private S5Pdo $db,private string $sql){}
 public function execute(?array $params=null):bool {
  $this->db->log[]=[$this->sql,$params??[]];
  $this->pending=[];
  foreach($this->db->fetchQueue as $needle=>$sets){
   if(str_contains($this->sql,$needle)&&$sets!==[]){$this->pending=array_shift($this->db->fetchQueue[$needle]);break;}
  }
  $this->rows=str_starts_with(ltrim($this->sql),'SELECT')?0:$this->db->affected;
  return true;
 }
 public function fetch(int $mode=PDO::FETCH_DEFAULT,int $cursorOrientation=PDO::FETCH_ORI_NEXT,int $cursorOffset=0):mixed {
  return $this->pending===[]?false:array_shift($this->pending);
 }
 public function fetchAll(int $mode=PDO::FETCH_DEFAULT,mixed ...$args):array {return $this->pending;}
 public function fetchColumn(int $column=0):mixed {
  foreach($this->db->scalars as $needle=>$value)if(str_contains($this->sql,$needle))return $value;
  return false;
 }
 public function rowCount():int {return $this->rows;}
}

final class S5Pdo extends PDO {
 public array $log=[];
 /** @var array<string,list<list<array>>> needle => queued result sets, one per matching execute */
 public array $fetchQueue=[];
 /** @var array<string,mixed> */
 public array $scalars=['GET_LOCK'=>1,'RELEASE_LOCK'=>1,'DATABASE()'=>'titan_booking_test_a'];
 public int $affected=1;
 public bool $inside=false;
 public int $commits=0;
 public int $rollbacks=0;
 public function __construct(){}
 public function getAttribute(int $attribute):mixed {
  return match($attribute){PDO::ATTR_DRIVER_NAME=>'mysql',PDO::ATTR_EMULATE_PREPARES=>false,default=>null};
 }
 public function query(string $query,?int $fetchMode=null,mixed ...$fetchModeArgs):PDOStatement|false {
  $s=new S5Statement($this,$query);$s->execute([]);return $s;
 }
 public function prepare(string $query,array $options=[]):PDOStatement|false {return new S5Statement($this,$query);}
 public function beginTransaction():bool {$this->inside=true;return true;}
 public function commit():bool {$this->inside=false;$this->commits++;return true;}
 public function rollBack():bool {$this->inside=false;$this->rollbacks++;return true;}
 public function inTransaction():bool {return $this->inside;}
 public function sqlLog():array {return array_column($this->log,0);}
 public function writes():array {
  return array_values(array_filter($this->sqlLog(),static fn(string $sql)=>(bool)preg_match('/^\s*(INSERT|UPDATE|DELETE)\b/i',$sql)));
 }
}

final class S5Reflection {
 public static function unconnectedPdo():PDO {return (new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();}
}

const S5_OWNER_ID=12;
const S5_BOOKING_ID=42;
function s5_flags(array $overrides=[]):array {
 return array_merge([
  'booking_writes_enabled'=>true,'booking_schema_verified'=>true,'booking_rate_mapping_verified'=>true,
  'booking_concurrency_verified'=>true,'owner_operations_enabled'=>true,'authenticated_owner_id'=>S5_OWNER_ID,
  'database'=>'titan_booking_test_a','verified_pricing_settings'=>[],
 ],$overrides);
}
function s5_cancel_payload(array $extra=[]):array {
 return array_merge(['booking_id'=>S5_BOOKING_ID,'idempotency_key'=>'cancel-key-s5-0000000001','reason'=>'Client unavailable'],$extra);
}
function s5_reschedule_payload(array $extra=[]):array {
 return array_merge([
  'booking_id'=>S5_BOOKING_ID,'start_at'=>'2020-01-06 09:00:00','duration_minutes'=>60,
  'timezone'=>'Australia/Melbourne','staff_ids'=>[2],'idempotency_key'=>'resched-key-s5-0000000001','reason'=>'Moved',
 ],$extra);
}
function s5_row(string $status,string $staff='2,5',string $start='2026-11-20 09:00:00'):array {
 return ['id'=>S5_BOOKING_ID,'order_id'=>900,'staff_ids'=>$staff,'booking_date_time'=>$start,'booking_status'=>$status];
}
function s5_snapshot_row(string $status,string $staff='2,5',string $start='2026-11-20 09:00:00'):array {
 return ['id'=>S5_BOOKING_ID,'order_id'=>900,'booking_status'=>$status,'booking_date_time'=>$start,'staff_ids'=>$staff];
}
const S5_SNAPSHOT_NEEDLE='SELECT id,order_id,booking_status';
const S5_LOCKED_NEEDLE='ct_bookings WHERE id=? FOR UPDATE';
const S5_LEDGER_NEEDLE='FROM titan_booking_requests';

// 1. Default off: no flags, or any flag missing, is unsupported and never touches a database.
$unconfigured=S5Reflection::unconnectedPdo();
foreach(['booking_cancel','booking_reschedule'] as $op){
 s5_throws(fn()=>(new Dispatcher($unconfigured))->dispatch($op,[]),InvalidArgumentException::class,"$op default-off must be unsupported");
 foreach(['booking_writes_enabled','booking_schema_verified','booking_rate_mapping_verified','booking_concurrency_verified','owner_operations_enabled'] as $missing){
  $config=s5_flags();unset($config[$missing]);
  s5_throws(fn()=>(new Dispatcher($unconfigured,$config))->dispatch($op,$op==='booking_cancel'?s5_cancel_payload():s5_reschedule_payload()),InvalidArgumentException::class,"$op with $missing off must be unsupported");
 }
}
echo "ok default-off gate\n";

// 2. Submission safety: caller-supplied authority is rejected before any database access.
foreach(['actor','role','owner_id','authenticated_owner_id','tenant_id','database','price','total','payment_status','booking_status','order_id','verified_pricing_settings','owner_operations_enabled'] as $field){
 $db=new S5Pdo();
 s5_throws(fn()=>(new Dispatcher($db,s5_flags()))->dispatch('booking_cancel',s5_cancel_payload([$field=>'x'])),InvalidArgumentException::class,"cancel must reject $field");
 s5_throws(fn()=>(new Dispatcher($db,s5_flags()))->dispatch('booking_reschedule',s5_reschedule_payload([$field=>'x'])),InvalidArgumentException::class,"reschedule must reject $field");
 s5_check($db->log===[],"privileged $field reached the database");
}
echo "ok submission safety\n";

// 3. Command validation and state rules.
s5_throws(fn()=>BookingCancellationInput::parse(s5_cancel_payload(['reason'=>"bad\x01reason"])),InvalidArgumentException::class,'control character reason');
s5_throws(fn()=>BookingCancellationInput::parse(s5_cancel_payload(['booking_id'=>'42'])),InvalidArgumentException::class,'string booking id');
s5_throws(fn()=>BookingCancellationInput::parse(s5_cancel_payload(['idempotency_key'=>'short'])),InvalidArgumentException::class,'short key');
s5_throws(fn()=>BookingCancellationInput::parse(s5_cancel_payload(['reason'=>str_repeat('r',201)])),InvalidArgumentException::class,'long reason');
echo "ok command validation\n";

// 4. Cancel through the dispatcher with flags on: fixture DB, owner identity from config, tenant pin enforced.
$db=new S5Pdo();
$db->fetchQueue[S5_SNAPSHOT_NEEDLE]=[[s5_snapshot_row('A')]];
$db->fetchQueue[S5_LOCKED_NEEDLE]=[[s5_row('A')],[s5_row('CS')]];
$db->fetchQueue[S5_LEDGER_NEEDLE]=[[]];
$result=(new Dispatcher($db,s5_flags()))->dispatch('booking_cancel',s5_cancel_payload());
s5_check($result===['booking_id'=>S5_BOOKING_ID,'replayed'=>false,'status'=>'cancelled'],'cancel result shape');
$sql=$db->sqlLog();
$locks=array_values(array_filter($sql,static fn($s)=>str_contains($s,'GET_LOCK')));
s5_check(count($locks)===3,'booking lock plus two staff locks expected');
s5_check($db->commits===1&&$db->rollbacks===0,'cancel must commit once');
$updates=array_values(array_filter($db->log,static fn($e)=>str_contains($e[0],'UPDATE ct_bookings SET booking_status=?')));
s5_check(count($updates)===1,'exactly one status update');
s5_check($updates[0][1]===['CS','Client unavailable',S5_BOOKING_ID,'A'],'status update must be conditional on prior status and use server-side owner target');
s5_check(count(array_filter($db->log,static fn($e)=>str_contains($e[0],'SELECT RELEASE_LOCK')))===3,'all locks released');
echo "ok cancel flow\n";

// 5. Lock order: booking lock first, then the staff locks in sorted key order, then the transaction starts.
$keys=array_map(static fn($e)=>$e[1][0],array_values(array_filter($db->log,static fn($e)=>str_contains($e[0],'GET_LOCK'))));
s5_check(str_contains($keys[0],':booking:'.S5_BOOKING_ID),'booking lock must be acquired first');
s5_check(str_ends_with($keys[1],':staff:2')&&str_ends_with($keys[2],':staff:5'),'staff locks must be sorted and follow the booking lock');
echo "ok lock order\n";

// 6. Replay of a completed cancel returns the stored result and writes nothing.
$command=BookingCancellationInput::parse(s5_cancel_payload());
$fingerprint=BookingMutationFingerprint::of('cancel',['role'=>'owner','id'=>S5_OWNER_ID],$command);
$db=new S5Pdo();
$db->fetchQueue[S5_SNAPSHOT_NEEDLE]=[[s5_snapshot_row('CS')]];
$db->fetchQueue[S5_LEDGER_NEEDLE]=[[['fingerprint'=>$fingerprint,'state'=>'completed','booking_id'=>(string)S5_BOOKING_ID]]];
$db->fetchQueue[S5_LOCKED_NEEDLE]=[[s5_row('CS')]];
$result=(new Dispatcher($db,s5_flags()))->dispatch('booking_cancel',s5_cancel_payload());
s5_check($result===['booking_id'=>S5_BOOKING_ID,'replayed'=>true,'status'=>'cancelled'],'replay result');
s5_check($db->writes()===[],'replay must not write');
echo "ok replay\n";

// 7. Same key with a different reason is refused.
$db=new S5Pdo();
$db->fetchQueue[S5_SNAPSHOT_NEEDLE]=[[s5_snapshot_row('CS')]];
$db->fetchQueue[S5_LEDGER_NEEDLE]=[[['fingerprint'=>$fingerprint,'state'=>'completed','booking_id'=>(string)S5_BOOKING_ID]]];
s5_throws(fn()=>(new Dispatcher($db,s5_flags()))->dispatch('booking_cancel',s5_cancel_payload(['reason'=>'Different reason'])),DomainException::class,'reused key with different payload must fail');
s5_check($db->writes()===[],'reused key must not write');
echo "ok key reuse\n";

// 8. Staff changed between the unlocked snapshot and the locked requery: refused, rolled back, no UPDATE.
$db=new S5Pdo();
$db->fetchQueue[S5_SNAPSHOT_NEEDLE]=[[s5_snapshot_row('A','2,5')]];
$db->fetchQueue[S5_LEDGER_NEEDLE]=[[]];
$db->fetchQueue[S5_LOCKED_NEEDLE]=[[s5_row('A','2,7')]];
s5_throws(fn()=>(new Dispatcher($db,s5_flags()))->dispatch('booking_cancel',s5_cancel_payload()),DomainException::class,'staff change must be refused under lock');
s5_check($db->rollbacks===1&&$db->commits===0,'changed booking must roll back');
s5_check(!in_array(true,array_map(static fn($s)=>str_starts_with($s,'UPDATE ct_bookings'),$db->sqlLog()),true),'no status update after concurrent change');
echo "ok requery under lock\n";

// 9. Tenant pin: a connection to a different database than the server-selected tenant is refused before locks.
$db=new S5Pdo();
$db->scalars['DATABASE()']='titan_booking_test_b';
s5_throws(fn()=>(new Dispatcher($db,s5_flags()))->dispatch('booking_cancel',s5_cancel_payload()),DomainException::class,'tenant mismatch must be refused');
s5_check(!in_array(true,array_map(static fn($s)=>str_contains($s,'GET_LOCK'),$db->sqlLog()),true),'no lock before tenant pin passes');
echo "ok tenant pin\n";

// 10. Reschedule through the dispatcher with flags on: time and identity are validated under the write gate.
$db=new S5Pdo();
s5_throws(fn()=>(new Dispatcher($db,s5_flags()))->dispatch('booking_reschedule',s5_reschedule_payload()),DomainException::class,'past reschedule must be refused');
s5_check($db->writes()===[]&&!in_array(true,array_map(static fn($s)=>str_contains($s,'GET_LOCK'),$db->sqlLog()),true),'refused reschedule must not lock or write');
echo "ok reschedule gate\n";

// 11. The RequestHandler allowlist exposes the contract names.
$handler=file_get_contents(__DIR__.'/../../private/booking-sidecar/RequestHandler.php');
s5_check(str_contains($handler,"'booking_cancel','booking_reschedule'"),'allowlist missing contract names');
echo "pass05_booking_lifecycle_gate OK\n";
