<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Each dry-run has disjoint request keys for creation, conflict, reschedule and cancellation. */
final class LifecycleMutationKeys { public static function generate():array {$hex=bin2hex(random_bytes(12));return ['create'=>'lifecycle-'.$hex.'-create','conflict'=>'lifecycle-'.$hex.'-conflict','reschedule'=>'lifecycle-'.$hex.'-reschedule','cancel'=>'lifecycle-'.$hex.'-cancel'];} public static function distinct(array $keys):bool {return count($keys)===count(array_unique(array_values($keys),SORT_STRING));} }
