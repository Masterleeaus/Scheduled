<?php
declare(strict_types=1);
/* Behavioural test for the booking claim ledger. Always checks the claim ON CONFLICT DO NOTHING semantics on SQLite;
   runs the full claim/read/commit flow on real PostgreSQL DDL when TITAN_TEST_PGSQL_DSN is set (e.g. pgsql:host=127.0.0.1;dbname=scratch). */
require __DIR__.'/pass33_bootstrap.php';
foreach (glob(__DIR__.'/../../core/app/Booking/Public/Redemption/*.php') as $f) require_once $f;
use App\Booking\Public\Redemption\{RedeemLedger,RedeemSchema};
function ledger_ok(bool $result, string $what):void {if(!$result)throw new RuntimeException('Ledger check failed: '.$what);}
function run_ledger_checks(PDO $db, string $label):void {
    $db->exec('DROP TABLE IF EXISTS titan_booking_intent_claims');
    $db->exec($label==='pgsql' ? RedeemSchema::ddl() : 'CREATE TABLE titan_booking_intent_claims (claim_key CHAR(64) PRIMARY KEY, owner_id BIGINT NOT NULL, fingerprint CHAR(64) NOT NULL, state TEXT NOT NULL, booking_id BIGINT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
    $ledger=new RedeemLedger($db);
    $key=str_repeat('a',64);$fp=str_repeat('b',64);
    ledger_ok($ledger->claim($key,1,$fp)===true,"$label first claim");
    ledger_ok($ledger->claim($key,1,$fp)===false,"$label duplicate claim returns false without error");
    if($label!=='pgsql')return; // SQLite has no SELECT ... FOR UPDATE; read/commit are PostgreSQL-only checks
    $row=$ledger->read($key);
    ledger_ok($row!==null && $row['state']==='claimed',"$label read claimed");
    ledger_ok($ledger->commit($key,1,$fp,27)===true,"$label commit");
    ledger_ok($ledger->commit($key,1,$fp,27)===false,"$label second commit is a no-op");
    ledger_ok($ledger->read($key)['booking_id']==27,"$label booking id stored");
    $db->exec('DROP TABLE IF EXISTS titan_booking_intent_claims');
}
$sqlite=new PDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
run_ledger_checks($sqlite,'sqlite');
$dsn=getenv('TITAN_TEST_PGSQL_DSN')?:'';
if($dsn!==''){
    if(!preg_match('/^pgsql:[a-zA-Z0-9=;._-]+$/D',$dsn))throw new RuntimeException('Invalid TITAN_TEST_PGSQL_DSN');
    $pg=new PDO($dsn,getenv('TITAN_TEST_PGSQL_USER')?:'',getenv('TITAN_TEST_PGSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false]);
    run_ledger_checks($pg,'pgsql');
    echo "pgsql live checks: ran\n";
} else {
    echo "pgsql live checks: skipped (TITAN_TEST_PGSQL_DSN not set)\n";
}
echo 'PASS pass34_ledger_pgsql\n';
