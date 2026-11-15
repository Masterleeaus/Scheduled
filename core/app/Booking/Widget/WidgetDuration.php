<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetDuration { public static function normalize($value): int { if(!is_scalar($value)||!preg_match('/^[1-9][0-9]{0,3}$/D',(string)$value))throw new \InvalidArgumentException('Invalid duration'); $n=(int)$value;if($n<15||$n>720||$n%15!==0)throw new \InvalidArgumentException('Duration must be 15-minute increments');return $n; } }
