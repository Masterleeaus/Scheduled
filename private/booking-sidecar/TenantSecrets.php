<?php
namespace Titan\BookingSidecar;
use RuntimeException;
/** Maps random instance IDs to allowlisted tenant database credentials. No caller-selected DSNs. */
final class TenantSecrets {
    public function __construct(private string $path) {}
    public function get(string $key): array {
        if (!preg_match('/^[a-zA-Z0-9_-]{16,64}$/D', $key)) throw new RuntimeException('Unknown instance.');
        if (!is_file($this->path)) throw new RuntimeException('Sidecar tenant registry missing.');
        $rows = json_decode((string)file_get_contents($this->path), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($rows) || !isset($rows[$key]) || !is_array($rows[$key])) throw new RuntimeException('Unknown instance.');
        $config = $rows[$key];
        if (($config['enabled'] ?? false) !== true) throw new RuntimeException('Instance disabled.');
        foreach (['dsn','db_user','db_password'] as $field) if (!isset($config[$field]) || !is_string($config[$field])) throw new RuntimeException('Invalid tenant config.');
        if (!preg_match('/^mysql:host=[a-zA-Z0-9._-]+;port=[0-9]{2,5};dbname=[a-zA-Z0-9_]+;charset=utf8mb4$/D', $config['dsn'])) throw new RuntimeException('Unsafe database configuration.');
        return $config;
    }
}
