<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;
/** Implement only in a deployment verified against live disposable MariaDB acceptance.
 * The implementation owns private tenant configuration, a PDO transaction, verified
 * release evidence and the CleantoNativeGateway. Do not bind it from HTTP input.
 */
interface CheckoutSubmissionExecutor {
    public function commit(int $owner, array $selection, array $identity, array $personalInfo, string $intentNonce): array;
}
