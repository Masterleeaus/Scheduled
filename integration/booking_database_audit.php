<?php
declare(strict_types=1);
require __DIR__.'/../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingDatabaseCapabilityReader,BookingDatabaseIdentity,BookingSchemaColumnsReader,BookingSchemaAudit,BookingEngineRequirements,BookingSqlDiagnostics};
if(PHP_SAPI!=='cli'){fwrite(STDERR,"CLI only\n");exit(2);}
if(!in_array('mysql',PDO::getAvailableDrivers(),true)){fwrite(STDERR,"pdo_mysql unavailable; no live DB checks performed\n");exit(3);}
$dsn=getenv('TITAN_TEST_MYSQL_DSN')?:'';
if(!preg_match('/^mysql:host=[a-zA-Z0-9._-]+;port=[0-9]{2,5};dbname=[a-zA-Z0-9_]+;charset=utf8mb4$/D',$dsn)){fwrite(STDERR,"Set TITAN_TEST_MYSQL_DSN to a valid mysql DSN\n");exit(2);}
try {
 $db=new PDO($dsn,getenv('TITAN_TEST_MYSQL_USER')?:'',getenv('TITAN_TEST_MYSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false]);
 $schema=(new BookingSchemaAudit(new BookingSchemaColumnsReader($db)))->inspect();
 $cap=(new BookingDatabaseCapabilityReader($db))->inspect();
 $req=BookingEngineRequirements::check(['pdo_mysql'=>true,'server_supported'=>false,'innodb'=>$cap['booking_table_innodb'],'isolated'=>false,'schema'=>$schema['ok'],'pricing'=>false,'concurrency'=>false]);
 $summary=BookingSqlDiagnostics::summary($schema,$cap,$req);
 echo json_encode($summary,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n";
 exit($summary['schema_ok']?0:1);
} catch(Throwable $e){fwrite(STDERR,"Database audit failed (details withheld)\n");exit(1);}
