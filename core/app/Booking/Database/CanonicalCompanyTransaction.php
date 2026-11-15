<?php
declare(strict_types=1);

namespace App\Booking\Database;

/**
 * Native PostgreSQL boundary for booking data owned by a Central-verified company.
 *
 * The caller MUST resolve $companyId from an authenticated Central membership,
 * never from a customer-supplied parameter. The PostgreSQL role must be subject
 * to RLS and must not own tenant tables or have BYPASSRLS. This helper is NOT a
 * substitute for those database grants and policies.
 */
final class CanonicalCompanyTransaction
{
    public static function assertCompanyId(string $companyId): string
    {
        if (!preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/D',
            $companyId
        )) {
            throw new \InvalidArgumentException('Canonical company UUID required');
        }

        return $companyId;
    }

    /**
     * @template T
     * @param callable(\PDO):T $work
     * @return T
     */
    public static function run(\PDO $pdo, string $companyId, callable $work)
    {
        self::assertCompanyId($companyId);

        if ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
            throw new \RuntimeException('Native booking requires PostgreSQL');
        }
        if ($pdo->inTransaction()) {
            throw new \RuntimeException('An ambient booking transaction is not allowed');
        }

        if (!$pdo->beginTransaction()) {
            throw new \RuntimeException('Unable to begin booking transaction');
        }

        try {
            // SET LOCAL semantics: scope is reset by PostgreSQL on COMMIT/ROLLBACK.
            $set = $pdo->prepare("SELECT set_config('titan.company_id', :company_id, true)");
            if ($set === false || !$set->execute(['company_id' => $companyId])) {
                throw new \RuntimeException('Unable to establish company context');
            }

            $check = $pdo->query("SELECT current_setting('titan.company_id', true)");
            $activeCompanyId = $check === false ? false : $check->fetchColumn();
            if ($activeCompanyId !== $companyId) {
                throw new \RuntimeException('Company context verification failed');
            }

            $result = $work($pdo);
            if (!$pdo->commit()) {
                throw new \RuntimeException('Unable to commit booking transaction');
            }
            return $result;
        } catch (\Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }
}
