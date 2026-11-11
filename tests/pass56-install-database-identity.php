<?php
require __DIR__.'/../core/app/Support/Install/DatabaseConnectionIdentity.php';
use App\Support\Install\DatabaseConnectionIdentity as Check;

$valid = ['database_name'=>'clean','server_hostname'=>'db01','server_port'=>'3306','server_id'=>'1'];
$n = 0;
function ok(bool $assertion, string $label): void { global $n; if (!$assertion) {throw new Exception($label);} $n++; }
function rejects(callable $fn): bool { try {$fn();} catch (\RuntimeException $e) {return true;} return false; }
ok(Check::normalize($valid) === ['database'=>'clean','hostname'=>'db01','port'=>3306,'server_id'=>1], 'normalization');
ok(Check::assertSame($valid, $valid) === null, 'same database');
foreach (['database_name'=>'different','server_hostname'=>'db02','server_port'=>'3307','server_id'=>'2'] as $k=>$v) {
 $different=$valid; $different[$k]=$v;
 ok(rejects(fn()=>Check::assertSame($valid,$different)), 'reject identity mismatch '.$k);
}
foreach (['database_name'=>'','server_hostname'=>'','server_port'=>'0','server_id'=>'not-a-number'] as $k=>$v) {
 $different=$valid; $different[$k]=$v;
 ok(rejects(fn()=>Check::assertSame($different,$valid)), 'reject incomplete identity '.$k);
}
$controller=file_get_contents(__DIR__.'/../core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php');
$identity=strpos($controller,'DatabaseConnectionIdentity::assertMatching(');
$empty=strpos($controller,'LegacySqlImporter::ensureEmptyDatabase(');
$import=strpos($controller,'LegacySqlImporter::import(');
ok($identity !== false && $empty!==false && $import!==false && $identity<$empty && $identity<$import,'identity before DDL');
ok(strpos($controller, "\$settings['url'] = null;") !== false, 'url override');
ok(strpos($controller, "\$settings['unix_socket'] = '';") !== false, 'socket override');
ok(strpos($controller, "unset(\$settings['read'], \$settings['write']);") !== false, 'replica override');
echo "PASS {$n}/{$n} database identity assertions\n";
