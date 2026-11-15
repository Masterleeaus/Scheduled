<?php

namespace App\Support\Install;

/**
 * Read-only requirements that must hold before installation can be marked complete.
 * The original SQL importer is deliberately the sole fresh-install schema writer.
 */
final class InstallationCompletionGuard
{
    public static function keyIsValid(string $value): bool
    {
        if (strncmp($value, 'base64:', 7) !== 0) {
            return false;
        }
        $decoded = base64_decode(substr($value, 7), true);
        return is_string($decoded) && strlen($decoded) === 32;
    }

    /** @return list<string> */
    public static function problems(array $state): array
    {
        $issues = [];
        if (empty($state['schema_imported'])) {
            $issues[] = 'The schema import has not been confirmed for this installation session.';
        }
        if (empty($state['administrator_created'])) {
            $issues[] = 'The first administrator has not been confirmed for this installation session.';
        }
        if (empty($state['identity_schema_valid'])) {
            $issues[] = 'Required account tables are not available.';
        }
        if (($state['administrator_count'] ?? null) !== 1 || ($state['active_administrator_count'] ?? null) !== 1) {
            $issues[] = 'Exactly one active first administrator is required.';
        }
        if (($state['customer_count'] ?? null) !== 0) {
            $issues[] = 'A fresh install must not include inherited customer accounts.';
        }
        if (empty($state['administrator_hash_present'])) {
            $issues[] = 'The new administrator does not have a password hash.';
        }
        if (!self::keyIsValid((string) ($state['app_key'] ?? ''))) {
            $issues[] = 'The application encryption key is missing or invalid.';
        }
        if (empty($state['environment_readable']) || empty($state['storage_writable'])) {
            $issues[] = 'Application environment or storage permissions are invalid.';
        }
        if (!empty($state['installed_marker_exists'])) {
            $issues[] = 'This installation is already finalised.';
        }
        return $issues;
    }
}
