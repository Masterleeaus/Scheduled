<?php
namespace Titan\BookingSidecar;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
/** Half-open intervals: a booking ending exactly at the next start is not a conflict. */
final class Interval {
    public static function parse(string $value, string $zone = 'UTC'): DateTimeImmutable {
        if (!in_array($zone, DateTimeZone::listIdentifiers(), true)) throw new InvalidArgumentException('Invalid timezone.');
        $dt = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $value, new DateTimeZone($zone));
        if (!$dt || $dt->format('Y-m-d H:i:s') !== $value) throw new InvalidArgumentException('Invalid date/time.');
        return $dt;
    }
    public static function overlaps(int $aStart, int $aEnd, int $bStart, int $bEnd): bool {
        if ($aEnd <= $aStart || $bEnd <= $bStart) throw new InvalidArgumentException('Invalid interval.');
        return $aStart < $bEnd && $bStart < $aEnd;
    }
    public static function window(string $date, int $minutes, string $zone): array {
        $start = self::parse($date, $zone);
        if ($minutes < 1 || $minutes > 1440) throw new InvalidArgumentException('Invalid duration.');
        return [$start->getTimestamp(), $start->modify('+'.$minutes.' minutes')->getTimestamp()];
    }
}
