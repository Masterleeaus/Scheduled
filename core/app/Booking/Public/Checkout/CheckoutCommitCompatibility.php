<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;

use App\Booking\Public\Redemption\CommitLegacyClaimKey;
use App\Booking\Public\Redemption\CommitLegacySelection;
use App\Booking\Public\Redemption\CommitCustomerIdentityPolicy;
use Titan\BookingSidecar\BookingPersonalInfo;

/**
 * Verifies that a public checkout can be represented by the native Cleanto
 * transaction writer. This only validates a candidate; it never authorizes
 * a payment, enables writes, or treats the preview intent as a booking.
 */
final class CheckoutCommitCompatibility {
    public static function assert(array $selection, array $customer, string $intentNonce): void {
        $mapped = CheckoutNativeCustomer::map($customer);
        $safe = $mapped['identity'];
        $claimKey = CommitLegacyClaimKey::derive($intentNonce);
        CommitLegacySelection::map($selection, $safe, $claimKey);
        // Verify native Cleanto compatibility without initiating a booking.
        // The redemption route must pass this data through the trusted command
        // when public booking writes are independently authorized.
        BookingPersonalInfo::encode($mapped['personal_info']);
    }
}
