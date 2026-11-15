<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetTimeZone { public static function normalize($value): string { if(!is_string($value)||strlen($value)>80||!in_array($value,\DateTimeZone::listIdentifiers(),true))throw new \InvalidArgumentException('Invalid timezone'); return $value; } }
