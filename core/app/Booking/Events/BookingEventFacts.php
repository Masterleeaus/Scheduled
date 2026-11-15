<?php
declare(strict_types=1);

namespace App\Booking\Events;

use App\Central\CentralException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Builds the payload of one booking event from the caller's values. Only the fields listed here can appear in a
 * payload, so customer personal data cannot reach Central by accident. customerRef must be an opaque token.
 */
final class BookingEventFacts
{
    public const MAX_STAFF = 20;

    private const ID = '/\A[A-Za-z0-9_-]{1,64}\z/';
    private const UUID = '/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/i';
    private const STATUS = '/\A[a-z][a-z_]{0,31}\z/';
    private const TIME = '/\A\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,6})?(Z|[+-]\d{2}:\d{2})\z/';
    private const TIMEZONE = '/\A[A-Za-z]+(\/[A-Za-z0-9_+-]+){1,2}\z/';

    /**
     * @param array{bookingId:string,companyId:string,occurredAt:string,serviceId:string,staffIds:list<string>,
     *   startAt:string,endAt:string,timezone:string,status:string,customerRef:string} $input
     * @return array<string,mixed> payload with UTC millisecond times, ready for canonical JSON
     */
    public static function payload(array $input): array
    {
        $allowed = ['bookingId', 'companyId', 'occurredAt', 'serviceId', 'staffIds', 'startAt', 'endAt', 'timezone', 'status', 'customerRef'];
        $unknown = array_diff(array_keys($input), $allowed);
        if ($unknown !== []) {
            throw new CentralException('invalid_payload', 'Booking event input has fields that are not allowed.');
        }
        foreach ($allowed as $name) {
            if (!array_key_exists($name, $input)) {
                throw new CentralException('invalid_payload', 'Booking event input is missing a required field.');
            }
        }

        $companyId = self::string($input['companyId'], self::UUID, 'companyId');
        $start = self::time($input['startAt'], 'startAt');
        $end = self::time($input['endAt'], 'endAt');
        if ($end <= $start) {
            throw new CentralException('invalid_field', 'endAt must be after startAt.');
        }
        $staff = $input['staffIds'];
        if (!is_array($staff) || !array_is_list($staff) || count($staff) > self::MAX_STAFF) {
            throw new CentralException('invalid_field', 'staffIds must be a list of at most '.self::MAX_STAFF.' ids.');
        }
        $staffIds = [];
        foreach ($staff as $id) {
            $staffIds[] = self::string($id, self::ID, 'staffIds');
        }
        $staffIds = array_values(array_unique($staffIds));
        sort($staffIds, SORT_STRING);

        $timezone = self::string($input['timezone'], self::TIMEZONE, 'timezone');
        if (@timezone_open($timezone) === false) {
            throw new CentralException('invalid_field', 'timezone is not an IANA identifier.');
        }

        return [
            'bookingId' => self::string($input['bookingId'], self::ID, 'bookingId'),
            'companyId' => $companyId,
            'occurredAt' => self::time($input['occurredAt'], 'occurredAt'),
            'serviceId' => self::string($input['serviceId'], self::ID, 'serviceId'),
            'staffIds' => $staffIds,
            'startAt' => $start,
            'endAt' => $end,
            'timezone' => $timezone,
            'status' => self::string($input['status'], self::STATUS, 'status'),
            'customerRef' => self::string($input['customerRef'], self::ID, 'customerRef'),
        ];
    }

    private static function string(mixed $value, string $pattern, string $field): string
    {
        if (!is_string($value) || preg_match($pattern, $value) !== 1) {
            throw new CentralException('invalid_field', $field.' has an invalid format.');
        }

        return $value;
    }

    /** Returns the instant as UTC with milliseconds, e.g. 2026-11-15T09:30:00.000Z. */
    private static function time(mixed $value, string $field): string
    {
        $text = self::string($value, self::TIME, $field);
        try {
            $instant = new DateTimeImmutable($text);
        } catch (\Exception) {
            throw new CentralException('invalid_field', $field.' is not a valid time.');
        }

        return $instant->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.v\Z');
    }
}
