<?php
declare(strict_types=1);

namespace App\Central\Link;

/**
 * Maps a verified Central subject to an existing local user. Implementations must return an id only for an
 * existing, verified application link. They must never create a user, and they must never guess from an
 * email address or a name.
 */
interface CentralUserLinkResolver
{
    /** Returns the local user id for a verified (issuer, subject) pair, or null when no link exists. */
    public function localUserId(string $issuer, string $subject): ?int;
}
