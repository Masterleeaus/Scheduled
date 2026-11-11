<?php
namespace App\Support\Install;

use Illuminate\Database\Connection;
use RuntimeException;

/**
 * Before non-transactional DDL, compare the SQL-import connection to the
 * Laravel migration connection. A stale DATABASE_URL/socket/replica must never
 * silently create the two halves of a fresh install on different servers.
 */
final class DatabaseConnectionIdentity
{
    /** @return array{database:string, hostname:string, port:int, server_id:int} */
    public static function normalize(array $row): array
    {
        $database = $row['database_name'] ?? null;
        $hostname = $row['server_hostname'] ?? null;
        $port = $row['server_port'] ?? null;
        $serverId = $row['server_id'] ?? null;
        if (!is_string($database) || $database === '' || !is_string($hostname) || $hostname === ''
            || !is_numeric($port) || (int)$port < 1 || (int)$port > 65535
            || !is_numeric($serverId) || (int)$serverId < 0) {
            throw new RuntimeException('Installation database identity was incomplete.');
        }
        return ['database' => $database, 'hostname' => $hostname,
            'port' => (int)$port, 'server_id' => (int)$serverId];
    }

    public static function assertSame(array $import, array $migration): void
    {
        if (self::normalize($import) !== self::normalize($migration)) {
            throw new RuntimeException('SQL-import and migration connections do not identify the same database server.');
        }
    }

    public static function assertMatching(\mysqli $import, Connection $migration): void
    {
        // Query both independently. Neither can be trusted just because the
        // requested database name matches; two servers can have the same name.
        $sql = 'SELECT DATABASE() AS database_name, @@hostname AS server_hostname, '
            .'@@port AS server_port, @@server_id AS server_id';
        $mysqlResult = $import->query($sql);
        if (!$mysqlResult) {
            throw new RuntimeException('Cannot inspect SQL-import database identity.');
        }
        try {
            $importIdentity = $mysqlResult->fetch_assoc();
        } finally {
            $mysqlResult->free();
        }
        try {
            $pdoIdentity = $migration->selectOne($sql);
        } catch (\Throwable $e) {
            throw new RuntimeException('Cannot inspect migration database identity.', 0, $e);
        }
        if (!is_array($importIdentity) || !is_object($pdoIdentity)) {
            throw new RuntimeException('Installation database identity query returned no data.');
        }
        self::assertSame($importIdentity, (array)$pdoIdentity);
    }
}
