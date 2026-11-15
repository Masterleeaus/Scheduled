<?php
/** Private booking runtime: deploy behind network ACL, outside public document root. */
declare(strict_types=1);
spl_autoload_register(static function (string $class): void {
    $prefix = 'Titan\\BookingSidecar\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;
    $file = __DIR__ . '/' . substr($class, strlen($prefix)) . '.php';
    if (is_file($file)) require_once $file;
});
