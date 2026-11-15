<?php
declare(strict_types=1);
spl_autoload_register(static function (string $class): void {
    $prefix = 'Titan\\BookingIntegration\\';
    if (!str_starts_with($class, $prefix)) return;
    $path = __DIR__ . '/' . substr($class, strlen($prefix)) . '.php';
    if (is_file($path)) require_once $path;
});
