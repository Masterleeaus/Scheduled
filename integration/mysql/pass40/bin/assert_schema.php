<?php
declare(strict_types=1);
/** Compare existing Cleanto and integration tables before a destructive acceptance run. */
final class Pass40Schema {
 public const TABLES=['ct_bookings','ct_order_client_info','ct_services','ct_services_methods_units_rate','titan_booking_intent_claims'];
 public static function missing(array $tables):array {return array_values(array_diff(self::TABLES,$tables));}
 public static function validateEngines(array $engines):void {foreach(self::TABLES as $table){if(strtolower((string)($engines[$table]??''))!=='innodb')throw new DomainException('Nontransactional or missing table: '.$table);}}
}
if(realpath($_SERVER['SCRIPT_FILENAME']??'')===__FILE__){
 if(!in_array('mysql',PDO::getAvailableDrivers(),true)){fwrite(STDERR,'SKIP: pdo_mysql missing'.PHP_EOL);exit(77);}
 $host=getenv('BOOKING_TEST_DB_HOST')?:'';$name=getenv('BOOKING_TEST_DB_NAME')?:'';
 if(!in_array($host,['mysql-test','localhost','127.0.0.1'],true)||$name!=='titan_booking_test_acceptance'){fwrite(STDERR,'FAIL: unsafe database scope'.PHP_EOL);exit(2);}
 try{$pdo=new PDO('mysql:host='.$host.';dbname='.$name.';charset=utf8mb4',getenv('BOOKING_TEST_DB_USER')?:'',getenv('BOOKING_TEST_DB_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $stmt=$pdo->query('SELECT TABLE_NAME,ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE()');$eng=[];
 foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r)$eng[$r['TABLE_NAME']]=$r['ENGINE'];
 Pass40Schema::validateEngines($eng);echo "PASS: required tables use InnoDB
";
 }catch(Throwable $e){fwrite(STDERR,'FAIL: database not ready or schema mismatch'.PHP_EOL);exit(2);}
}
