<?php
require_once __DIR__.'/../core/app/Support/Install/LegacySqlImporter.php';
use App\Support\Install\LegacySqlImporter;
$ok = 0;
$cases = [
    "-- comment\nCREATE TABLE t (n INT); # trailing\nINSERT INTO t VALUES (1);" => 2,
    "INSERT INTO t VALUES ('semi;colon'),('quote\\'here'); SELECT 1;" => 2,
    "/* block; comment */ SELECT 1; /*!40101 SET NAMES utf8mb4 */;" => 2,
    "SELECT 'double''quote', \"x;y\";" => 1,
];
foreach ($cases as $sql => $expected) {
    $rows = iterator_to_array(LegacySqlImporter::statements($sql));
    if (count($rows) !== $expected) { throw new RuntimeException('SQL parser failed fixture'); }
    $ok++;
}
$root = dirname(__DIR__);
$wizard = file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php');
$finish = file_get_contents($root.'/core/resources/views/vendor/installer/finished.blade.php');
$manager = file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Helpers/EnvironmentManager.php');
$conf = file_get_contents($root.'/core/config/installer.php');
$asserts = [
    !str_contains($wizard, "copy('core/vendor/league/flysystem/mockery.php'"),
    !str_contains($wizard, "copy('core/vendor/league/flysystem/machie.php'"),
    str_contains($wizard, 'LegacySqlImporter::import'),
    !str_contains($finish, '$finalEnvFile'),
    !str_contains($finish, 'Password: </strong>admin'),
    str_contains($conf, "'pdo_mysql'"),
    str_contains($manager, "'APP_ENV' => 'production'"),
    str_contains($manager, "'APP_DEBUG' => 'false'"),
    !str_contains($manager, 'INDIPAY_SALT'),
];
foreach ($asserts as $pass) { if (!$pass) { throw new RuntimeException('Installer safety contract failed'); } $ok++; }
// PayUmoney salt: the dump must carry the placeholder, and the env var must supply it.
$dumpSql = file_get_contents($root.'/installer/database.sql');
if (!str_contains($dumpSql, LegacySqlImporter::PAYUMONEY_SALT_PLACEHOLDER)) { throw new RuntimeException('Dump lacks salt placeholder'); }
$ok++;
putenv(LegacySqlImporter::PAYUMONEY_SALT_ENV);
try { LegacySqlImporter::resolveSeedSecrets($dumpSql); throw new RuntimeException('Missing salt was accepted'); }
catch (RuntimeException $e) { if (str_contains($e->getMessage(), 'Missing salt')) { throw $e; } $ok++; }
putenv(LegacySqlImporter::PAYUMONEY_SALT_ENV.'=bad salt;');
try { LegacySqlImporter::resolveSeedSecrets($dumpSql); throw new RuntimeException('Malformed salt was accepted'); }
catch (RuntimeException $e) { if (str_contains($e->getMessage(), 'accepted')) { throw $e; } $ok++; }
putenv(LegacySqlImporter::PAYUMONEY_SALT_ENV.'=test-salt-0001');
$resolved = LegacySqlImporter::resolveSeedSecrets($dumpSql);
if (str_contains($resolved, LegacySqlImporter::PAYUMONEY_SALT_PLACEHOLDER) || !str_contains($resolved, '\"salt\":\"test-salt-0001\"')) { throw new RuntimeException('Salt substitution failed'); }
$ok++;
putenv(LegacySqlImporter::PAYUMONEY_SALT_ENV);
$statements = 0;
foreach (LegacySqlImporter::statements(file_get_contents($root.'/installer/database.sql')) as $stmt) {
    $statements++;
}
if ($statements < 50) { throw new RuntimeException('Schema parsing unexpectedly returned too few statements'); }
$ok++;
// Payment gateway seed (#54): the dump must not carry live or test gateway credentials. Every secret-bearing
// field in the payment_gateways rows must be empty or a named placeholder such as <PAYUMONEY_SALT>.
$gatewayRows = 0;
$inGateways = false;
foreach (explode("\n", (string) file_get_contents($root.'/installer/database.sql')) as $line) {
    if (str_starts_with($line, 'INSERT INTO `payment_gateways`')) { $inGateways = true; continue; }
    if (!$inGateways) { continue; }
    if (preg_match("/'(\\{.*\\})'/", $line, $m) !== 1) { continue; }
    $gatewayRows++;
    $json = json_decode(str_replace('\\"', '"', $m[1]), true);
    if (!is_array($json)) { throw new RuntimeException('Gateway seed row is not readable JSON'); }
    foreach ($json as $field => $value) {
        if (!is_string($value) || preg_match('/^(text|sandbox_check|email|website|industry)$/i', (string) $field)) { continue; }
        if (preg_match('/key|secret|salt|token|client_id|public|password|merchant/i', (string) $field) === 1
            && $value !== '' && preg_match('/^<[A-Z_]+>$/', $value) !== 1) {
            throw new RuntimeException("Gateway seed commits a credential in field {$field}");
        }
    }
    if (str_ends_with(rtrim($line), ';')) { $inGateways = false; }
}
if ($gatewayRows < 10) { throw new RuntimeException('Gateway seed rows were not found'); }
$ok++;
echo "Pass 33 installer safety checks: {$ok}/{$ok} passed; {$statements} schema statements parsed.\n";
