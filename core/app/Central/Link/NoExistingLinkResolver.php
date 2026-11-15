<?php
declare(strict_types=1);

namespace App\Central\Link;

/** Fail-closed fallback until trusted, organisation-scoped links are provisioned. */
final class NoExistingLinkResolver implements CentralUserLinkResolver
{
    public function localUserId(string $issuer, string $subject, string $organisationId): ?int
    {
        return null;
    }
}
