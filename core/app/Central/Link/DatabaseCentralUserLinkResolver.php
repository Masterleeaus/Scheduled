<?php
declare(strict_types=1);

namespace App\Central\Link;

use Illuminate\Support\Facades\DB;

/**
 * Existing-user account links approved and provisioned by a trusted operator.
 *
 * A verified OIDC issuer+subject is the only lookup input. Email, display names and
 * caller-submitted user ids are never used. No account is created by this resolver.
 * Revocation is authoritative at read time; missing table/DB failures fail closed.
 */
final class DatabaseCentralUserLinkResolver implements CentralUserLinkResolver
{
    /** @var null|\Closure(string,string):mixed */
    private readonly ?\Closure $lookup;

    /** @param null|\Closure(string,string):mixed $lookup Inject only for unit tests. */
    public function __construct(?\Closure $lookup = null)
    {
        $this->lookup = $lookup;
    }

    public function localUserId(string $issuer, string $subject): ?int
    {
        if ($issuer === '' || $subject === '' || strlen($issuer) > 2048 || strlen($subject) > 2048) {
            return null;
        }

        $issuerDigest = hash('sha256', $issuer);
        $subjectDigest = hash('sha256', $subject);
        $candidate = $this->lookup !== null
            ? ($this->lookup)($issuerDigest, $subjectDigest)
            : DB::table('central_user_links')
                ->where('issuer_sha256', $issuerDigest)
                ->where('subject_sha256', $subjectDigest)
                ->whereNull('revoked_at')
                ->value('user_id');

        if (!is_int($candidate) && !(is_string($candidate) && ctype_digit($candidate))) {
            return null;
        }
        $id = filter_var($candidate, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $id === false ? null : $id;
    }
}
