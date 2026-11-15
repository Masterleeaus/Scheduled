<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;

use App\Booking\Public\Redemption\CommitCleantoTenantPolicy;
use App\Booking\Public\Redemption\CommitReleaseEvidencePolicy;

/** Strict, server-owned deployment mapping. A profile is NOT evidence that the live gates passed. */
final class CheckoutDeploymentProfile
{
    public static function tenant(int $owner, array $profiles): array
    {
        if ($owner < 1 || !array_key_exists($owner, $profiles) || !is_array($profiles[$owner])) {
            throw new \RuntimeException('Booking tenant configuration unavailable');
        }
        $profile = $profiles[$owner];
        $keys = ['connection', 'tenant', 'release_evidence'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $profile)) {
                throw new \RuntimeException('Incomplete trusted booking configuration');
            }
        }
        $connection = $profile['connection'];
        if (!is_string($connection) || !preg_match('/^[A-Za-z][A-Za-z0-9_-]{0,63}$/D', $connection)) {
            throw new \RuntimeException('Invalid trusted booking connection');
        }
        if (!is_array($profile['tenant']) || !is_array($profile['release_evidence'])) {
            throw new \RuntimeException('Invalid trusted booking release settings');
        }
        CommitCleantoTenantPolicy::validate($owner, $profile['tenant']);
        CommitReleaseEvidencePolicy::validate($profile['release_evidence']);
        return $profile;
    }
}
