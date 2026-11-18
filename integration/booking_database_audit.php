<?php
declare(strict_types=1);
require __DIR__.'/../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingDatabaseCapabilityReader,BookingDatabaseIdentity,BookingSchemaColumnsReader,BookingSchemaAudit,BookingEngineRequirements,BookingSqlDiagnostics};
if(PHP_SAPI!=='cli'){fwrite(STDERR,"CLI only\n");exit(2);}
if(!in_array('pgsql',PDO::getAvailableDrivers(),true)){fwrite(STDERR,"pdo_pgsql unavailable; no live DB checks performed\n");exit(3);}
$dsn=getenv('TITAN_TEST_PGSQL_DSN')?:'';
if(!preg_match('/^pgsql:host=[a-zA-Z0-9._-]+;port=[0-9]{2,5};dbname=[a-zA-Z0-9_]+$/D',$dsn)){fwrite(STDERR,"Set TITAN_TEST_PGSQL_DSN to a valid pgsql DSN\n");exit(2);}
try {
 $db=new PDO($dsn,getenv('TITAN_TEST_PGSQL_USER')?:'',getenv('TITAN_TEST_PGSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false]);
 $schema=(new BookingSchemaAudit(new BookingSchemaColumnsReader($db)))->inspect();
 $cap=(new BookingDatabaseCapabilityReader($db))->inspect();
 $req=BookingEngineRequirements::check(['pdo_pgsql'=>true,'server_supported'=>false,'innodb'=>$cap['transactional_engine'],'isolated'=>false,'schema'=>$schema['ok'],'pricing'=>false,'concurrency'=>false]);
 $summary=BookingSqlDiagnostics::summary($schema,$cap,$req);
 echo json_encode($summary,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n";
 exit($summary['schema_ok']?0:1);
} catch(Throwable $e){fwrite(STDERR,"Database audit failed (details withheld)\n");exit(1);}
