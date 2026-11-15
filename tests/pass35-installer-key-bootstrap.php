<?php
require_once $argv[1] ?? __DIR__ . '/../core/bootstrap/InstallerBootKey.php';
function verdict(bool $ok, string $name): void { if (!$ok) {fwrite(STDERR,"FAIL: $name\n");exit(1);}echo "OK: $name\n"; }
$base = sys_get_temp_dir().'/plusagency-installer-'.bin2hex(random_bytes(6));
mkdir($base.'/storage', 0700, true);
file_put_contents($base.'/.env.example', "APP_ENV=local\nAPP_DEBUG=true\nAPP_KEY=\nDB_HOST=127.0.0.1\n");
try {
    InstallerBootKey::ensure($base);
    $content=file_get_contents($base.'/.env');
    verdict(preg_match('/^APP_KEY=base64:([A-Za-z0-9+\\/=]+)/m',$content,$m)===1 && strlen(base64_decode($m[1],true))===32,'cryptographically generated 32-byte key');
    verdict(str_contains($content,'APP_ENV=production') && str_contains($content,'APP_DEBUG=false'),'safe initial env');
    InstallerBootKey::ensure($base);
    verdict(file_get_contents($base.'/.env')===$content,'idempotent after second bootstrap');
    file_put_contents($base.'/storage/installed', 'complete');
    file_put_contents($base.'/.env', "APP_KEY=\n");
    InstallerBootKey::ensure($base);
    verdict(file_get_contents($base.'/.env')==="APP_KEY=\n",'never rotates/recreates on installed site');
} finally {
    @unlink($base.'/storage/installed');@unlink($base.'/.env');@unlink($base.'/.env.example');@rmdir($base.'/storage');@rmdir($base);
}
