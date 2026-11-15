<?php
// Run in an extracted archive: php tests/pass43-webroot-protection.php
$root=dirname(__DIR__);
$rules=file_get_contents($root.'/.htaccess');
$checks=[
    'core blocked'=>str_contains($rules,'^(?:core|private|tools|tests|docs|'),
    'SQL blocked'=>str_contains($rules,'installer/database\\.sql'),
    'HTTP php tooling blocked'=>str_contains($rules,'^(?:core|private|tools|tests|docs|'),
    'Nginx example'=>is_file($root.'/docs/deployment/nginx-security.conf'),
    'front controller preserved'=>is_file($root.'/index.php'),
    'SQL dump retained for importer'=>is_file($root.'/installer/database.sql'),
    'preflight protection check'=>str_contains(file_get_contents($root.'/tools/install-preflight.php'),'Apache private-source deny rules'),
];
foreach($checks as $label=>$ok){echo ($ok?'PASS':'FAIL').' '.$label."\n";}
exit(in_array(false,$checks,true)?1:0);
