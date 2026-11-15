<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogUnique {public static function assertIds(array $rows):void {$ids=[];foreach($rows as $r){$id=CatalogId::parse($r['id']??null);if(isset($ids[$id]))throw new \InvalidArgumentException('Duplicate catalog id');$ids[$id]=true;}} }
