<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetServiceSelection { public static function normalize($value): int { if (!is_scalar($value) || !preg_match('/^[1-9][0-9]{0,9}$/D', (string)$value)) throw new \InvalidArgumentException('Invalid service reference'); return (int)$value; } }
