<?php
declare(strict_types=1);
require dirname(__DIR__).'/core/app/Support/Install/LegacySqlImporter.php';
use App\Support\Install\LegacySqlImporter as Importer;
$path = dirname(__DIR__).'/installer/database.sql';
$info = Importer::preflight($path);
assert($info['statements'] > 400);
assert($info['skipped_demo_inserts'] >= 2);
$temporary = tempnam(sys_get_temp_dir(), 'pa-install-');
try {
    file_put_contents($temporary, "CREATE TABLE `admins` (id INT); CREATE TABLE `users` (id INT);");
    try { Importer::preflight($temporary); throw new RuntimeException('Should reject incomplete schema'); }
    catch (RuntimeException $e) { if (!str_contains($e->getMessage(), 'roles')) throw $e; }
    file_put_contents($temporary, "CREATE TABLE `admins` (id INT); CREATE TABLE `users` (id INT); CREATE TABLE `roles` (id INT); INSERT INTO `admins` VALUES (1); DROP DATABASE other;");
    try { Importer::preflight($temporary); throw new RuntimeException('Should reject destructive operation'); }
    catch (RuntimeException $e) { if (!str_contains($e->getMessage(), 'prohibited')) throw $e; }
} finally { @unlink($temporary); }
echo "PASS: schema profile and malicious/incomplete dump regression; ".$info['statements']." statements, ".$info['skipped_demo_inserts']." skipped login inserts\n";
