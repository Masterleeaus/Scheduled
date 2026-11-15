<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogUnit {public static function fromRow(array $r):array {return ['id'=>CatalogId::parse($r['id']??null),'method_id'=>CatalogId::parse($r['method_id']??null),'label'=>CatalogLabel::parse($r['title']??null)];} }
