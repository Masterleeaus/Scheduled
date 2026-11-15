<?php

namespace App\Support\Install;

use RuntimeException;

/**
 * MySQL connection-scoped lock protecting the entire one-time schema import.
 * The owner connection remains open while PDO runs the Laravel feature migrations.
 * Named locks are server-wide, so different HTTP workers cannot import concurrently.
 */
final class InstallationDatabaseLock
{
    public static function name(string $databaseName): string
    {
        if ($databaseName === '' || strlen($databaseName) > 64) {
            throw new RuntimeException('Invalid installation database identity.');
        }
        return 'pa_install_'.substr(hash('sha256', $databaseName), 0, 40);
    }

    private static function identity(\mysqli $db): string
    {
        $result = $db->query('SELECT DATABASE() AS database_name');
        if (!$result) {
            throw new RuntimeException('Cannot identify the installation database.');
        }
        try {
            $row = $result->fetch_assoc();
            $databaseName = $row['database_name'] ?? null;
            if (!is_string($databaseName) || $databaseName === '') {
                throw new RuntimeException('An installation database must be selected.');
            }
            return self::name($databaseName);
        } finally {
            $result->free();
        }
    }

    public static function acquire(\mysqli $db): void
    {
        $name = self::identity($db);
        $result = $db->query("SELECT GET_LOCK('".$name."', 5) AS acquired");
        if (!$result) {
            throw new RuntimeException('Cannot acquire the installation database lock.');
        }
        try {
            $row = $result->fetch_assoc();
            if ((int) ($row['acquired'] ?? 0) !== 1) {
                throw new RuntimeException('Another installation is using this database. Retry when it has finished.');
            }
        } finally {
            $result->free();
        }
    }

    public static function release(\mysqli $db): void
    {
        $name = self::identity($db);
        $result = $db->query("SELECT RELEASE_LOCK('".$name."') AS released");
        if (!$result) {
            throw new RuntimeException('Cannot release the installation database lock.');
        }
        try {
            $row = $result->fetch_assoc();
            if ((int) ($row['released'] ?? 0) !== 1) {
                throw new RuntimeException('Installation database lock ownership was lost.');
            }
        } finally {
            $result->free();
        }
    }
}
