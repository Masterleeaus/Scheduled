<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueStaffRows {public static function normalize(array $rows):array {$out=[];$ids=[];foreach($rows as $row){$id=CatalogueIdentifier::parse($row['id']??null);if(isset($ids[$id]))throw new \DomainException('Duplicate staff identifier');$ids[$id]=true;$name=trim((string)($row['first_name']??'').' '.(string)($row['last_name']??''));$out[]=['id'=>$id,'label'=>CatalogueLabel::normalize($name)];}return $out;}}
