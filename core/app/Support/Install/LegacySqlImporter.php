<?php

namespace App\Support\Install;

use RuntimeException;

/**
 * One-time PlusAgency legacy schema loader.
 * Does not accept a user-selected SQL file or permit imports to an existing database.
 * MySQL DDL isn't generally rollbackable; fail closed before any import if tables exist.
 */
final class LegacySqlImporter
{
    public static function ensureEmptyDatabase(\mysqli $db): void
    {
        $result = $db->query('SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = DATABASE() AND table_type = \'BASE TABLE\'');
        if (! $result) {
            throw new RuntimeException('Cannot inspect target database tables.');
        }
        $row = $result->fetch_assoc();
        $result->free();
        if ((int) ($row['n'] ?? -1) !== 0) {
            throw new RuntimeException('Installation requires a dedicated empty database; existing tables were detected. Nothing was imported.');
        }
    }

    /**
     * Validate the bundled legacy dump before executing any DDL.
     * This is deliberately strict: unexpected dumps require a reviewed migration,
     * not a best-effort import into customer data.
     * @return array{statements:int,skipped_demo_inserts:int}
     */
    public static function preflight(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException('Bundled installation schema is missing or unreadable.');
        }
        $sql = file_get_contents($path);
        if (! is_string($sql) || trim($sql) === '') {
            throw new RuntimeException('Bundled installation schema is empty.');
        }
        $seen = [];
        $count = 0;
        $skipped = 0;
        foreach (self::statements($sql) as $statement) {
            $count++;
            if (preg_match('/^\s*(?:DROP\s+DATABASE|CREATE\s+DATABASE|USE\s+[`\w]+|LOAD\s+DATA\s+LOCAL\s+INFILE)\b/i', $statement)) {
                throw new RuntimeException('Bundled installation schema contains a prohibited database operation.');
            }
            if (preg_match('/^CREATE\s+TABLE\s+`?([a-z_][a-z0-9_]*)`?/i', $statement, $matches)) {
                $seen[strtolower($matches[1])] = true;
            }
            if (self::isDemoAccountInsert($statement)) { $skipped++; }
        }
        foreach (['admins', 'users', 'roles'] as $required) {
            if (empty($seen[$required])) {
                throw new RuntimeException('Bundled installation schema is missing the '.$required.' table.');
            }
        }
        if ($count < 3 || $skipped < 1) {
            throw new RuntimeException('Bundled installation schema did not match the expected fresh-install profile.');
        }
        return ['statements' => $count, 'skipped_demo_inserts' => $skipped];
    }

    /** Ensure the fresh import produced the tables, but no inherited credentials. */
    public static function verifyFreshIdentityTables(\mysqli $db): void
    {
        foreach (['admins', 'users', 'roles'] as $table) {
            $result = $db->query('SELECT COUNT(*) AS n FROM `'.$table.'`');
            if (! $result) {
                throw new RuntimeException('Installation is missing a required account table.');
            }
            $row = $result->fetch_assoc();
            $result->free();
            if ($table !== 'roles' && (int) ($row['n'] ?? -1) !== 0) {
                throw new RuntimeException('Fresh installation must not contain seeded login accounts.');
            }
        }
    }

    /** @return int Count of statements executed. */
    public static function import(\mysqli $db, string $path): int
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException('Bundled installation schema is missing or unreadable.');
        }
        self::preflight($path);
        self::ensureEmptyDatabase($db);
        $sql = file_get_contents($path);
        if ($sql === false || $sql === '') {
            throw new RuntimeException('Bundled installation schema is empty.');
        }
        $count = 0;
        foreach (self::statements($sql) as $statement) {
            // The purchased demo dump contains known user/admin passwords and hashes.
            // Never seed login identities into a fresh customer installation.
            if (self::isDemoAccountInsert($statement)) {
                continue;
            }
            $count++;
            if (! $db->query($statement)) {
                // Never print the SQL statement; seed data may contain account details.
                throw new RuntimeException('Schema import stopped at SQL statement '.$count.': '.$db->error.'. Database may be partially installed; recreate an empty database before retrying.');
            }
        }
        if ($count === 0) {
            throw new RuntimeException('Schema import had no SQL statements.');
        }
        self::verifyFreshIdentityTables($db);
        return $count;
    }

    /** Reject ONLY account identity seed INSERTs; preserve structure and all other data. */
    public static function isDemoAccountInsert(string $sql): bool
    {
        return (bool) preg_match('/^\s*(?:INSERT|REPLACE)\s+INTO\s+`?(?:admins|users)`?\s*(?:\(|VALUES|SET)/i', $sql);
    }

    /**
     * Streaming SQL statement splitting with quoted strings and comments.
     * MySQL conditional executable comments are retained for charset directives.
     * @return \Generator<string>
     */
    public static function statements(string $sql): \Generator
    {
        $len = strlen($sql);
        $buffer = '';
        $quote = null;
        $lineComment = false;
        $blockComment = false;
        for ($i = 0; $i < $len; $i++) {
            $c = $sql[$i];
            $n = $i + 1 < $len ? $sql[$i + 1] : '';
            if ($lineComment) {
                if ($c === "\n") { $lineComment = false; $buffer .= "\n"; }
                continue;
            }
            if ($blockComment) {
                if ($c === '*' && $n === '/') { $blockComment = false; $i++; }
                continue;
            }
            if ($quote !== null) {
                $buffer .= $c;
                if ($c === '\\' && $i + 1 < $len) { $buffer .= $sql[++$i]; continue; }
                if ($c === $quote) {
                    if ($n === $quote) { $buffer .= $sql[++$i]; }
                    else { $quote = null; }
                }
                continue;
            }
            if ($c === "'" || $c === '"' || $c === '`') { $quote = $c; $buffer .= $c; continue; }
            if ($c === '-' && $n === '-' && ($i + 2 === $len || ctype_space($sql[$i + 2]))) { $lineComment = true; $i++; continue; }
            if ($c === '#') { $lineComment = true; continue; }
            if ($c === '/' && $n === '*') {
                if ($i + 2 < $len && $sql[$i + 2] === '!') {
                    // Preserve executable MySQL comments, including their semicolon delimiters.
                    $end = strpos($sql, '*/', $i + 3);
                    if ($end === false) { throw new RuntimeException('Unclosed MySQL directive in schema.'); }
                    $buffer .= substr($sql, $i, $end + 2 - $i);
                    $i = $end + 1;
                } else { $blockComment = true; $i++; }
                continue;
            }
            if ($c === ';') {
                $statement = trim($buffer);
                if ($statement !== '') { yield $statement; }
                $buffer = '';
                continue;
            }
            $buffer .= $c;
        }
        if ($quote !== null || $blockComment) {
            throw new RuntimeException('Unterminated quoted text or comment in bundled SQL schema.');
        }
        if (trim($buffer) !== '') { yield trim($buffer); }
    }
}
