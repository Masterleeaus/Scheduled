<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetQuantity { public static function normalize($value): int { if(!is_scalar($value)||!preg_match('/^[1-9][0-9]{0,3}$/D',(string)$value))throw new \InvalidArgumentException('Invalid quantity');$n=(int)$value;if($n>100)throw new \InvalidArgumentException('Quantity exceeds limit');return $n; } }
