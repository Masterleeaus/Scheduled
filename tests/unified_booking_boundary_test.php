<?php
declare(strict_types=1);

// Standalone smoke/contract checks. Run: php tests/unified_booking_boundary_test.php
// Does not require Laravel, a database or an external booking provider.
function config(string $key, $fallback = null)
{
    return $key === 'booking_internal.unified_mode' ? true : $fallback;
}

require_once dirname(__DIR__) . '/core/app/Booking/Database/CanonicalCompanyTransaction.php';
require_once dirname(__DIR__) . '/core/app/Booking/InternalBookingClient.php';
require_once dirname(__DIR__) . '/core/app/Booking/Public/Checkout/CheckoutSubmissionExecutor.php';
require_once dirname(__DIR__) . '/core/app/Booking/Public/Checkout/NativeCheckoutSubmissionExecutor.php';

use App\Booking\Database\CanonicalCompanyTransaction;
use App\Booking\InternalBookingClient;
use App\Booking\Public\Checkout\NativeCheckoutSubmissionExecutor;

$checks = 0;
$expectFailure = static function (callable $call, string $expected) use (&$checks): void {
    try {
        $call();
    } catch (\Throwable $e) {
        if ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException) {
            if (str_contains($e->getMessage(), $expected)) {
                $checks++;
                return;
            }
        }
        throw new \RuntimeException('Unexpected failure: ' . $e->getMessage(), 0, $e);
    }
    throw new \RuntimeException('Expected refusal was not raised: ' . $expected);
};

$uuid = '123e4567-e89b-42d3-a456-426614174000';
if (CanonicalCompanyTransaction::assertCompanyId($uuid) !== $uuid) {
    throw new \RuntimeException('Valid canonical UUID was rejected');
}
$checks++;
$expectFailure(static fn () => CanonicalCompanyTransaction::assertCompanyId('1 OR true'), 'Canonical company UUID');
$expectFailure(static fn () => CanonicalCompanyTransaction::assertCompanyId(''), 'Canonical company UUID');
$expectFailure(static fn () => CanonicalCompanyTransaction::assertCompanyId(strtoupper($uuid)), 'Canonical company UUID');

$expectFailure(
    static fn () => (new InternalBookingClient())->call(['instance_key' => 'legacy'], 'availability', []),
    'Legacy booking sidecar is prohibited'
);
$expectFailure(
    static fn () => (new NativeCheckoutSubmissionExecutor([], '/should-not-be-used'))->commit(
        1, [], [], [], str_repeat('a', 32)
    ),
    'PostgreSQL-native booking writer is not yet available'
);

echo 'PASS: ' . $checks . " unified booking boundary checks\n";
