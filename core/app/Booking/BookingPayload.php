<?php

namespace App\Booking;

use InvalidArgumentException;

/** Strictly normalizes the safe subset of slot-lookup inputs. */
final class BookingPayload
{
    public static function slots(array $input): array
    {
        $date = $input['date'] ?? null;
        $service = $input['service_id'] ?? null;
        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date)) {
            throw new InvalidArgumentException('Date must be ISO YYYY-MM-DD.');
        }
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException('Invalid calendar date.');
        }
        if (filter_var($service, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new InvalidArgumentException('Invalid service identifier.');
        }
        return ['date' => $date, 'service_id' => (int) $service];
    }
}
