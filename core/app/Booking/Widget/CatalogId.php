<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogId { public static function parse($v):int {if(!is_int($v)&&!is_string($v))throw new \InvalidArgumentException('Invalid catalog id'); if(!preg_match('/^[1-9][0-9]{0,9}$/D',(string)$v))throw new \InvalidArgumentException('Invalid catalog id'); $n=(int)$v;if($n>2147483647)throw new \InvalidArgumentException('Catalog id too large');return $n;} }
