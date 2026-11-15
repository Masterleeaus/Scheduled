<?php
/** Pass 49 source privacy contract: run php tests/pass49-private-http-protection.php */
declare(strict_types=1);
$root=dirname(__DIR__);
$apache=file_get_contents($root.'/.htaccess');
$nginx=file_get_contents($root.'/docs/deployment/nginx-security.conf');
$probe=file_get_contents($root.'/integration/install/pass47/http-security-check.sh');
$preflight=file_get_contents($root.'/tools/install-preflight.php');
function require49(bool $ok,string $description):void {
  if(!$ok){fwrite(STDERR,"FAIL: $description\n");exit(1);}
  echo "PASS: $description\n";
}
foreach(['core','private','tools','tests','docs','integration','development','scripts','PASS05_EVIDENCE'] as $dir){
  require49(str_contains($apache, $dir) && str_contains($nginx, $dir), "Apache and Nginx both deny $dir");
}
foreach(['PASS[0-9]+','RECOVERY_STATUS_PASS'] as $marker){
  require49(str_contains($apache,$marker) && str_contains($nginx,$marker), "Apache and Nginx both deny $marker root documents");
}
foreach(['/private/cleanto/vendor/composer/installed.json','/private/cleanto/assets/stripe/composer.json','/PASS48_REPORT.md','/PASS48_MANIFEST.json','/RECOVERY_STATUS_PASS62.json','/integration/install/pass47/compose.yaml'] as $privatePath){
  require49(str_contains($probe,$privatePath), "Live HTTP probe tests $privatePath");
}
require49(str_contains($preflight,'core|private|tools|tests|docs|integration|development|scripts|PASS05_EVIDENCE'), 'Preflight checks private-source protection');
require49(str_contains($probe,'403|404'), 'Probe accepts only deny/not-found for private paths');
require49(str_contains($apache,'RewriteRule ^ index.php [L]'), 'Public Laravel entrypoint preserved');
require49(str_contains($nginx,'location = /index.php'), 'Public Nginx PHP entrypoint preserved');
echo "PASS: Pass 49 source privacy checks\n";
