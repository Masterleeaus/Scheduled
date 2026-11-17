<?php
declare(strict_types=1);

namespace App\Central\Link;

use Illuminate\Support\Facades\DB;

/**
 * Explicit approved user-to-Central links, scoped to the verified organisation.
 *
 * No email-based matching, no account creation, no client-selected organisation.
 * Old links without an approved Central organisation remain inaccessible after
 * the additive migration until an authorised operator backfills and approves them.
 */
final class DatabaseCentralUserLinkResolver implements CentralUserLinkResolver
{
    /** @var null|\Closure(string,string,string):mixed */
    private readonly ?\Closure $lookup;

    /** @param null|\Closure(string,string,string):mixed $lookup Test seam only. */
    public function __construct(?\Closure $lookup = null)
    {
        $this->lookup = $lookup;
    }

    public function localUserId(string $issuer, string $subject, string $organisationId): ?int
    {
        if ($issuer === '' || $subject === '' || strlen($issuer) > 2048 || strlen($subject) > 2048
            || !self::isOrganisationUuid($organisationId)) {
            return null;
        }

        $issuerDigest = hash('sha256', $issuer);
        $subjectDigest = hash('sha256', $subject);
        $organisationId = strtolower($organisationId);
        $candidate = $this->lookup !== null
            ? ($this->lookup)($issuerDigest, $subjectDigest, $organisationId)
            : DB::table('central_user_links')
                ->where('issuer_sha256', $issuerDigest)
                ->where('subject_sha256', $subjectDigest)
                ->where('central_organisation_id', $organisationId)
                ->whereNull('revoked_at')
                ->value('user_id');

        if (!is_int($candidate) && !(is_string($candidate) && ctype_digit($candidate))) {
            return null;
        }
        $id = filter_var($candidate, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $id === false ? null : $id;
    }

    public static function isOrganisationUuid(mixed $id): bool
    {
        return is_string($id) && preg_match('/\A[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}\z/D', $id) === 1;
    }
}
