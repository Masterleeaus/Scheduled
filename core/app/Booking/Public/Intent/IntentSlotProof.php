<?php
declare(strict_types=1);
namespace App\Booking\Public\Intent;

/** A preview intent can reference only a currently published start for this staff/day/duration. */
final class IntentSlotProof {
    public static function validate(array $selection, array $availability): void {
        if (($availability['preview_only'] ?? null) !== true ||
            ($availability['booking_writes_enabled'] ?? null) !== false ||
            ($availability['appointment_created'] ?? null) !== false) {
            throw new \RuntimeException('Unsafe availability response');
        }
        $slots = $availability['available_starts'] ?? null;
        if (!is_array($slots) || !array_is_list($slots) || count($slots) > 120) {
            throw new \RuntimeException('Invalid availability response');
        }
        $zone = new \DateTimeZone($selection['timezone']);
        $matches = 0;
        foreach ($slots as $iso) {
            if (!is_string($iso) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:Z|[+-]\d{2}:\d{2})$/D', $iso)) {
                throw new \RuntimeException('Malformed slot');
            }
            try { $dt = new \DateTimeImmutable($iso); } catch (\Exception $e) {
                throw new \RuntimeException('Malformed slot', 0, $e);
            }
            if ($dt->setTimezone($zone)->format('Y-m-d\TH:i:s') === $selection['date_time']) $matches++;
        }
        if ($matches !== 1) throw new \InvalidArgumentException('Chosen time no longer available');
    }
}
