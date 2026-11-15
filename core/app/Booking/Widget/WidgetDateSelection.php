<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetDateSelection { public static function normalize($value): string { if(!is_string($value)||!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$value))throw new \InvalidArgumentException('Invalid date'); $d=\DateTimeImmutable::createFromFormat('!Y-m-d',$value); if(!$d||$d->format('Y-m-d')!==$value)throw new \InvalidArgumentException('Invalid date'); return $value; } }
