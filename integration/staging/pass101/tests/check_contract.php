<?php
declare(strict_types=1);
$dir=dirname(__DIR__);
function check101(bool $ok, string $message): void { if (!$ok) {fwrite(STDERR,"FAIL: $message\n");exit(1);} }
$compose=file_get_contents($dir.'/compose.yaml');
$script=file_get_contents($dir.'/acceptance.sh');
$launch=file_get_contents($dir.'/run.sh');
check101(strpos($compose,'internal: true')!==false,'network must be internal');
check101(strpos($compose,'ports:')===false,'no forwarded database ports');
check101(strpos($compose,'tmpfs:')!==false,'database must be ephemeral');
check101(strpos($compose,'docker.sock')===false,'no host socket');
check101(strpos($compose,'../../..:/workspace:ro')!==false,'read-only source');
check101(strpos($compose,':/fixtures:ro')!==false,'read-only fixtures');
check101(strpos($script,'run_concurrency.php')!==false,'must execute real concurrency');
check101(strpos($script,'run_lifecycle.php')!==false,'must execute real booking lifecycle');
check101(strpos($script,'preflight.php')!==false,'must use existing preflight');
check101(strpos($script,'run_acceptance.php')!==false,'must check DB infrastructure');
check101(strpos($launch,'down --volumes')!==false,'cleanup needed');
check101(strpos($launch,'docker compose')!==false,'docker runner expected');
check101(strpos($launch,'PA101_SCHEMA_SQL')!==false,'authentic schema input must be required');
check101(strpos($launch,'PA101_SEED_SQL')!==false,'synthetic seed SQL must be required');
check101(strpos($launch,'PA101_DISPOSABLE_ACK')!==false,'explicit destructive test acknowledgement required');
check101(strpos($launch,'trap cleanup EXIT')!==false,'teardown on any exit required');
check101(strpos($launch,'exec -T mariadb-test')!==false,'internal SQL import required');
check101(strpos($launch,'< "$sql"')!==false,'SQL must stream via stdin');
check101(strpos($launch,'--wait mariadb-test')!==false,'database readiness required');
echo "PASS101 staging contract: 19/19\n";
