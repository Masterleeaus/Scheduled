<?php
declare(strict_types=1);
namespace App\Booking\Public\Intent;

/** Prevent issuing preview tokens for slots already in the past. */
final class IntentTimeProof {
    public static function assertFuture(array $selection, int $now): void {
        $zone = new \DateTimeZone($selection['timezone']);
        $local = $selection['date_time'];
        $instant = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s', $local, $zone);
        if (!$instant || $instant->format('Y-m-d\TH:i:s') !== $local) {
            throw new \InvalidArgumentException('Invalid appointment time');
        }
        // The schedule parser has already rejected nonexistent/ambiguous local times.
        if ($instant->getTimestamp() <= $now) {
            throw new \InvalidArgumentException('Appointment time has passed');
        }
    }
}
