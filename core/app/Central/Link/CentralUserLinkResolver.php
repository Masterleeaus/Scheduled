<?php
declare(strict_types=1);

namespace App\Central\Link;

/**
 * Resolve a verified OIDC identity and company to an explicitly approved local user.
 * The organisation must originate from a signature-verified identity token, not the browser.
 */
interface CentralUserLinkResolver
{
    public function localUserId(string $issuer, string $subject, string $organisationId): ?int;
}
