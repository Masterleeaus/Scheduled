<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogLimits {public static function check(array $s,array $m,array $u,array $staff):void {if(count($s)>250||count($m)>1000||count($u)>3000||count($staff)>250)throw new \InvalidArgumentException('Catalog too large');} }
