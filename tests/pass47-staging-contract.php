<?php
declare(strict_types=1);
$root = dirname(__DIR__).'/integration/install/pass47/';
function assert47(bool $ok, string $message): void { if (!$ok) {fwrite(STDERR,"FAIL: $message\n"); exit(1);} }
$compose=file_get_contents($root.'compose.yaml');
$runner=file_get_contents($root.'run.sh');
$docker=file_get_contents($root.'Dockerfile');
$http=file_get_contents($root.'http-security-check.sh');
$entry=file_get_contents($root.'entrypoint.sh');
$checks=[
 'loopback-bound HTTP' => str_contains($compose,'127.0.0.1:${PA47_HTTP_PORT:-18080}:80'),
 'private isolated network' => str_contains($compose,'internal: true'),
 'no host Docker socket' => !str_contains($compose,'docker.sock'),
 'no public database port' => !str_contains(substr($compose, strpos($compose, "\n  db:")), "\n    ports:"),
 'source mounted read-only' => str_contains($compose,'../../../:/source:ro'),
 'app executes disposable copy' => str_contains($entry,'cp -a /source/. /var/www/html/'),
 'ephemeral MariaDB data' => str_contains($compose,'/var/lib/mysql:rw,noexec,nosuid'),
 'native MySQL extensions' => str_contains($docker,'pdo_mysql mysqli'),
 'Apache rewrite enabled' => str_contains($docker,'a2enmod rewrite'),
 'explicit destructive acknowledgment' => str_contains($runner,'YES_DESTROY_DISPOSABLE_DB'),
 'no source secret' => str_contains($compose,'PA47_DB_PASSWORD:?'),
 'unique root and app passwords' => str_contains($runner,'PA47_DB_PASSWORD" != "$PA47_ROOT_PASSWORD'),
 'teardown on exit' => str_contains($runner,'trap cleanup EXIT'),
 'volume cleanup' => str_contains($runner,'down --volumes'),
 'PHP driver probe' => str_contains($runner,'pdo_mysql'),
 'application preflight' => str_contains($runner,'install-preflight.php --json'),
 'private-file HTTP probes' => str_contains($http,'/installer/database.sql') && str_contains($http,'/core/composer.json'),
 'fail HTTP 200 on private path' => str_contains($http,'403|404'),
 'no production readiness claim' => str_contains($runner,'verify admin login'),
];
foreach($checks as $name=>$ok)assert47((bool)$ok,$name);
echo 'PASS: '.count($checks).' staging acceptance contract assertions'.PHP_EOL;
