<?php
// Run from the extracted cumulative master: php integration/tests/pass79/deployment_preflight.php
$root=dirname(__DIR__,3);$issues=[];$notes=[];
foreach(['core/artisan','core/composer.json','core/routes/web.php','core/database/migrations/2026_11_10_000003_create_titan_site_templates.php','private/cleanto/objects/class_booking.php'] as $f){if(!is_file("$root/$f"))$issues[]="Missing application file: $f";}
if(PHP_VERSION_ID<80000)$issues[]='PHP 8.0 or newer is required by the added integration code';
foreach(['pdo_mysql','openssl','json'] as $ext){if(!extension_loaded($ext))$issues[]="Missing PHP extension: $ext";}
if(!is_dir("$root/core/vendor"))$issues[]='Laravel vendor dependencies not installed';
if(!is_file("$root/core/.env"))$issues[]='Application .env not present (install credentials must not be shipped)';
if(!is_file("$root/PASS58_REPORT.md"))$issues[]='Pass 58 source provenance remains unverified';
$notes[]='Checks are local file/extension prerequisites only, not installation, migration, or browser acceptance.';
foreach($issues as $issue)fwrite(STDERR,"BLOCKED: $issue\n");
echo json_encode(['status'=>$issues?'BLOCKED':'PREFLIGHT_ONLY','issues'=>$issues,'notes'=>$notes],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n";
exit($issues?77:0);
