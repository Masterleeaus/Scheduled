<?php
namespace App\Booking;
use InvalidArgumentException;
/** Internal contract only. A public tenant route must authorize staff ownership before use. */
final class BookingBusyRequest {
    public static function normalize(array $value): array {
        $staff=$value['staff_id']??null;
        if (filter_var($staff,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])===false) throw new InvalidArgumentException('Invalid staff id.');
        foreach (['from','until'] as $field) if (!is_string($value[$field]??null)) throw new InvalidArgumentException('Missing range.');
        $zone=$value['timezone']??'UTC';
        if (!is_string($zone)) throw new InvalidArgumentException('Invalid timezone.');
        return ['staff_id'=>(int)$staff,'from'=>$value['from'],'until'=>$value['until'],'timezone'=>$zone];
    }
}
