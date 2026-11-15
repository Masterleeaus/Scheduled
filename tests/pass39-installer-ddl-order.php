<?php
// Pass 39: executable structural guard against importing DDL before saving .env.
$root = dirname(__DIR__);
$controller = file_get_contents($root . '/core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php');
$importer = file_get_contents($root . '/core/app/Support/Install/LegacySqlImporter.php');
$checks = [
 'preflight before database write' => strpos($controller, 'LegacySqlImporter::preflight(') < strpos($controller, 'LegacySqlImporter::import('),
 'empty database before env write' => strpos($controller, 'LegacySqlImporter::ensureEmptyDatabase(') < strpos($controller, 'saveFileWizard($request)'),
 'env write before import' => strpos($controller, 'saveFileWizard($request)') < strpos($controller, 'LegacySqlImporter::import('),
 'failed env write throws' => str_contains($controller, 'Unable to save environment settings'),
 'same connection closed on failure' => str_contains($controller, 'finally {') && str_contains($controller, '$connection->close();'),
 'importer separately rechecks empty db' => str_contains($importer, 'self::ensureEmptyDatabase($db);'),
 'importer rejects demo accounts' => str_contains($importer, 'self::verifyFreshIdentityTables($db);'),
 'session marker after import' => strpos($controller, "session()->put('installer_schema_imported'") > strpos($controller, 'LegacySqlImporter::import('),
 'no application key regeneration' => !str_contains($controller, 'key:generate'),
 'schema importer skips demo login records' => str_contains($importer, 'self::isDemoAccountInsert($statement)'),
];
foreach ($checks as $label => $ok) {
    if (!$ok) { fwrite(STDERR, "FAIL $label\n"); exit(1); }
    echo "PASS $label\n";
}
echo 'PASS39: '.count($checks).'/'.count($checks)." checks passed\n";
