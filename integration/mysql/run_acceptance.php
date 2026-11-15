<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingIntegration\{MysqlTestConfig,MysqlDriverCheck,ConnectionPair,SchemaFailureReport,SchemaInventoryReader,TransactionalTableAudit,LockContentionProbe,RollbackProbe,AcceptanceDecision,AcceptanceExitCode,SessionSafetyProbe,RedactedFailure};
if(PHP_SAPI!=='cli'){http_response_code(403);exit('CLI only');}
if(!MysqlDriverCheck::available()){fwrite(STDERR,"SKIP: pdo_mysql unavailable; no DB test executed\n");exit(AcceptanceExitCode::SKIP);}
try{
 $cfg=MysqlTestConfig::fromEnvironment($_ENV+getenv());[$db,$second]=ConnectionPair::create($cfg);
 SessionSafetyProbe::assert($db);SessionSafetyProbe::assert($second);
 $schema=SchemaFailureReport::fromInventories(SchemaInventoryReader::read($db),TransactionalTableAudit::read($db));
 $locks=LockContentionProbe::run($db,$second);$transaction=['rollback'=>RollbackProbe::run($db)];
 $decision=AcceptanceDecision::evaluate($schema,$locks,$transaction,['safe'=>true]);
 echo json_encode(['decision'=>$decision,'schema'=>$schema,'lock_contention'=>$locks,'transaction'=>$transaction,'scope'=>'database_infrastructure_only','booking_creation_verified'=>false],JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR).PHP_EOL;
 exit(AcceptanceExitCode::forStatus(true,$decision));
}catch(Throwable $e){fwrite(STDERR,'FAIL: '.RedactedFailure::message($e).PHP_EOL);exit(AcceptanceExitCode::FAIL);}
