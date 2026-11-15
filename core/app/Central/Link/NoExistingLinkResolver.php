<?php
declare(strict_types=1);

namespace App\Central\Link;

/**
 * Default resolver. This repository holds no existing Central application link store, so every subject is
 * unlinked and sign-in is refused. It stays in place until an operator-approved link source is wired in.
 */
final class NoExistingLinkResolver implements CentralUserLinkResolver
{
    public function localUserId(string $issuer, string $subject): ?int
    {
        return null;
    }
}
