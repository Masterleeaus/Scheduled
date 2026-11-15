<?php
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Hard limits for read-only booking conflict lookup. */
final class ConflictPayload {
    public static function parse(array $payload): array {
        if (!filter_var($payload['staff_id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]])) throw new InvalidArgumentException('Invalid staff id.');
        foreach (['from','until'] as $key) {
            if (!is_string($payload[$key] ?? null) || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D',$payload[$key])) throw new InvalidArgumentException('Invalid timestamp.');
        }
        $zone=$payload['timezone']??'UTC';
        if (!is_string($zone)) throw new InvalidArgumentException('Invalid timezone.');
        $a=Interval::parse($payload['from'],$zone);$b=Interval::parse($payload['until'],$zone);
        if ($b <= $a || $b->getTimestamp()-$a->getTimestamp()>86400*8) throw new InvalidArgumentException('Invalid lookup window.');
        return ['staff_id'=>(int)$payload['staff_id'],'from'=>$payload['from'],'until'=>$payload['until'],'timezone'=>$zone];
    }
}
