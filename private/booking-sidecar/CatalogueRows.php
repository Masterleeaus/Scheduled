<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueRows {public static function normalize(array $rows,array $fields):array {$result=[];$seen=[];foreach($rows as $row){$id=CatalogueIdentifier::parse($row['id']??null);if(isset($seen[$id]))throw new \DomainException('Duplicate catalogue identifier');$seen[$id]=true;$normal=['id'=>$id,'label'=>CatalogueLabel::normalize($row['title']??null)];foreach($fields as $field)$normal[$field]=CatalogueIdentifier::parse($row[$field]??null);$result[]=$normal;}return $result;}}
