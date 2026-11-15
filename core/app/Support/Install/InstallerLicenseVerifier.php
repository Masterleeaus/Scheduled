<?php
namespace App\Support\Install;

/** Pure validation of the response from the official purchase-verification endpoint. */
final class InstallerLicenseVerifier
{
    public static function matches($payload, string $username, int $itemId): bool
    {
        if (!is_array($payload) || !isset($payload['buyer'], $payload['item'])
            || !is_array($payload['item']) || !isset($payload['item']['id'])) {
            return false;
        }
        return is_string($payload['buyer']) && hash_equals($username, $payload['buyer'])
            && filter_var($payload['item']['id'], FILTER_VALIDATE_INT) !== false
            && (int) $payload['item']['id'] === $itemId;
    }
}
