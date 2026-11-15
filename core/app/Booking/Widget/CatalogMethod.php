<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogMethod {public static function fromRow(array $r):array {return ['id'=>CatalogId::parse($r['id']??null),'service_id'=>CatalogId::parse($r['service_id']??null),'label'=>CatalogLabel::parse($r['title']??null)];} }
