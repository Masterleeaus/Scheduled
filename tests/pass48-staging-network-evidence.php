<?php
declare(strict_types=1);
$root=dirname(__DIR__).'/integration/install/pass47/';
$compose=file_get_contents($root.'compose.yaml');
$runner=file_get_contents($root.'run.sh');
$probe=file_get_contents($root.'http-security-check.sh');
$guide=file_get_contents($root.'README.md');
function check48(bool $ok,string $name):void { if(!$ok) {fwrite(STDERR,"FAIL $name\n");exit(1);} }
$assertions=[
 'app has private and egress networks'=>preg_match('/app:.*?networks:\s*- private\s*- egress/s',$compose)===1,
 'private network isolated'=>str_contains($compose,'internal: true'),
 'database private only'=>preg_match('/\n  db:.*?networks:\s*- private\s*restart:/s',$compose)===1,
 'database has no exposed ports'=>!preg_match('/\n  db:.*?\n    ports:/s',$compose),
 'HTTP only host loopback'=>str_contains($compose,'127.0.0.1:${PA47_HTTP_PORT:-18080}:80'),
 'no docker socket shared'=>!str_contains($compose,'docker.sock'),
 'runtime does not write mounted source'=>str_contains($compose,'../../../:/source:ro'),
 'Docker Compose preflight'=>str_contains($runner,'docker compose version'),
 'HTTP port validation'=>str_contains($runner,'PA47_HTTP_PORT must'),
 'unique database secrets'=>str_contains($runner,'PA47_DB_PASSWORD" != "$PA47_ROOT_PASSWORD'),
 'explicit disposable acknowledgement'=>str_contains($runner,'YES_DESTROY_DISPOSABLE_DB'),
 'unattended evidence mandatory'=>str_contains($runner,'Noninteractive mode requires'),
 'evidence files generated'=>str_contains($runner,'preflight.json')&&str_contains($runner,'http-security.txt'),
 'unattended run does not assert installation'=>str_contains($runner,'no administrator, template, payment or booking transaction'),
 'cleanup even on errors'=>str_contains($runner,'trap cleanup EXIT'),
 'deny integration config'=>str_contains($probe,'/integration/install/pass47/compose.yaml'),
 'deny logs'=>str_contains($probe,'/core/storage/logs/laravel.log'),
 'deny manifest'=>str_contains($probe,'/PASS47_MANIFEST.json'),
 'deny composer source'=>str_contains($probe,'/core/vendor/autoload.php'),
 'only 403/404 accepted'=>str_contains($probe,'403|404'),
 'documentation explains egress'=>str_contains($guide,'database-only `private` network'),
 'HTTP probe covers manifest'=>str_contains($probe,'PASS47_MANIFEST.json'),
];
foreach($assertions as $n=>$ok)check48((bool)$ok,$n);
echo 'PASS: '.count($assertions).' Pass 48 configuration assertions'.PHP_EOL;
