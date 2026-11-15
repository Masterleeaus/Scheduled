<?php
declare(strict_types=1);
namespace App\Booking\Diagnostics;

/** Inspect the authenticated tenant connection without executing any booking mutation. */
final class BookingSchemaSnapshot
{
    public static function inspect(\PDO $pdo, array $required): array
    {
        if ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) !== 'mysql') {
            throw new \RuntimeException('Only MySQL/MariaDB tenant connections can be inspected');
        }
        if ($pdo->inTransaction()) {
            throw new \RuntimeException('Do not inspect inside an ambient transaction');
        }
        $database = $pdo->query('SELECT DATABASE()')->fetchColumn();
        if (!is_string($database) || $database === '') {
            throw new \RuntimeException('No database selected');
        }
        $schema = [];
        $stmt = $pdo->prepare('SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, ORDINAL_POSITION');
        $stmt->execute([$database]);
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $table = (string) $row['TABLE_NAME'];
            $schema[$table][] = (string) $row['COLUMN_NAME'];
        }
        $missing = self::missing($required, $schema);
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        return ['database' => $database, 'server_version' => is_string($version) ? $version : '',
            'table_count' => count($schema), 'required_table_count' => count($required),
            'missing_columns' => $missing, 'schema_ok' => count($missing) === 0];
    }

    public static function missing(array $required, array $actual): array
    {
        $missing = [];
        foreach ($required as $table => $columns) {
            if (!is_string($table) || !is_array($columns)) throw new \InvalidArgumentException('Invalid schema contract');
            foreach ($columns as $column) {
                if (!is_string($column)) throw new \InvalidArgumentException('Invalid column contract');
                if (!in_array($column, $actual[$table] ?? [], true)) $missing[] = $table . '.' . $column;
            }
        }
        return $missing;
    }
}
