<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class DayOffPolicy {
 /** Legacy status convention uncertain: fail closed if ANY explicit off-day row exists. */
 public static function blocked(array $rows): bool {return count($rows)>0;}
}
