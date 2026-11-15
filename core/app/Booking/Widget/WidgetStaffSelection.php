<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetStaffSelection { public static function normalize($value): array { if (!is_array($value) || count($value)<1 || count($value)>12) throw new \InvalidArgumentException('Select 1-12 staff'); $ids=[]; foreach($value as $v) { if(!is_scalar($v)||!preg_match('/^[1-9][0-9]{0,9}$/D',(string)$v)) throw new \InvalidArgumentException('Invalid staff'); $ids[]=(int)$v; } if(count($ids)!==count(array_unique($ids)))throw new \InvalidArgumentException('Duplicate staff'); sort($ids,SORT_NUMERIC);return $ids; } }
