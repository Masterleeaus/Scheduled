<?php
declare(strict_types=1);
final class Pass40Locks {
 public static function plan(string $database,int $staffId):string {
  if($database!=='titan_booking_test_acceptance'||$staffId<1)throw new InvalidArgumentException('Unsafe lock scope');
  return 'titan_booking:'.substr(hash('sha256',$database),0,20).':staff:'.$staffId;
 }
 public static function assertDistinct(string $one,string $two):void {if($one===$two||$one===''||$two==='')throw new RuntimeException('Independent connection evidence missing');}
}
if(realpath($_SERVER['SCRIPT_FILENAME']??'')===__FILE__){
 if(!in_array('mysql',PDO::getAvailableDrivers(),true)){fwrite(STDERR,'SKIP: pdo_mysql missing'.PHP_EOL);exit(77);}
 $db=getenv('BOOKING_TEST_DB_NAME')?:'';$host=getenv('BOOKING_TEST_DB_HOST')?:'';
 if($db!=='titan_booking_test_acceptance'||!in_array($host,['mysql-test','localhost','127.0.0.1'],true)){fwrite(STDERR,"FAIL: unsafe scope
");exit(2);}
 $dsn='mysql:host='.$host.';dbname='.$db.';charset=utf8mb4';$u=getenv('BOOKING_TEST_DB_USER')?:'';$p=getenv('BOOKING_TEST_DB_PASSWORD')?:'';
 try{$a=new PDO($dsn,$u,$p,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);$b=new PDO($dsn,$u,$p,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $name=Pass40Locks::plan($db,42);Pass40Locks::assertDistinct((string)$a->query('SELECT CONNECTION_ID()')->fetchColumn(),(string)$b->query('SELECT CONNECTION_ID()')->fetchColumn());
 try{$hold=(int)$a->query('SELECT GET_LOCK('.$a->quote($name).',1)')->fetchColumn();$reject=(int)$b->query('SELECT GET_LOCK('.$b->quote($name).',0)')->fetchColumn();if($hold!==1||$reject!==0)throw new RuntimeException('Lock contention failure');}
 finally{$a->query('SELECT RELEASE_LOCK('.$a->quote($name).')');}
 $retry=(int)$b->query('SELECT GET_LOCK('.$b->quote($name).',1)')->fetchColumn();if($retry!==1)throw new RuntimeException('Lock not released');$b->query('SELECT RELEASE_LOCK('.$b->quote($name).')');echo "PASS: two-session lock exclusion
";
 }catch(Throwable $e){fwrite(STDERR,"FAIL: lock acceptance failed
");exit(2);}
}
