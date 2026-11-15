<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;

use App\Booking\Public\Redemption\CommitCleantoNativeGateway;
use Illuminate\Support\Facades\DB;

/** Executes only the pre-existing Cleanto transaction writer on a trusted tenant PDO connection. */
final class NativeCheckoutSubmissionExecutor implements CheckoutSubmissionExecutor
{
    private array $profiles;
    private string $privateRoot;

    public function __construct(array $profiles, string $privateRoot)
    {
        $this->profiles = $profiles;
        $this->privateRoot = $privateRoot;
    }

    public function commit(int $owner, array $selection, array $identity, array $personalInfo, string $intentNonce): array
    {
        // The existing Cleanto writer is MySQL-only. Never route a unified-mode booking
        // to that writer until a PostgreSQL-native implementation is accepted.
        if (config('booking_internal.unified_mode', false) === true) {
            throw new \RuntimeException('PostgreSQL-native booking writer is not yet available');
        }
        $profile = CheckoutDeploymentProfile::tenant($owner, $this->profiles);
        if (!preg_match('/^[a-f0-9]{32,128}$/D', $intentNonce)) {
            throw new \InvalidArgumentException('Invalid signed intent identifier');
        }
        // Choose the connection exclusively from trusted, server-owned tenant configuration.
        $connection = DB::connection($profile['connection']);
        $pdo = $connection->getPdo();
        if (!($pdo instanceof \PDO) || $pdo->inTransaction()) {
            throw new \RuntimeException('Isolated booking connection unavailable');
        }
        $result = CommitCleantoNativeGateway::execute(
            $pdo, $owner, $intentNonce, $selection, $identity,
            $profile['release_evidence'], $profile['tenant'], $this->privateRoot, $personalInfo
        );
        if (!is_array($result) || ($result['confirmed'] ?? null) !== true ||
            ($result['payment_settled'] ?? null) !== false ||
            !is_int($result['booking_id'] ?? null) || $result['booking_id'] < 1 ||
            !is_bool($result['replay'] ?? null)) {
            throw new \RuntimeException('Persisted booking proof unavailable');
        }
        return $result;
    }
}
