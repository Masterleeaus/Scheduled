<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogLabel {public static function parse($v):string {if(!is_string($v))throw new \InvalidArgumentException('Invalid catalog label');$v=trim($v);if($v===''||strlen($v)>160||preg_match('/[\x00-\x1f\x7f]/',$v))throw new \InvalidArgumentException('Invalid catalog label');return $v;} }
