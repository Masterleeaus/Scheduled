<?php
declare(strict_types=1);
namespace App\Booking\Public\Intent;
use App\Booking\Public\PublicDay;
use App\Booking\Public\PublicDuration;

/** Converts a validated selection to the narrow, read-only availability API contract. */
final class IntentRequestProof {
    public static function availabilityInput(array $selection): array {
        $date = substr($selection['date_time'], 0, 10);
        PublicDay::parse($date, $selection['timezone']);
        PublicDuration::parse($selection['duration_minutes']);
        return [
            'staff_id' => $selection['staff_id'],
            'date' => $date,
            'duration_minutes' => $selection['duration_minutes'],
            'timezone' => $selection['timezone'],
        ];
    }
}
