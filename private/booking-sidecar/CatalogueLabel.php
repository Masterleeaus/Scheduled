<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueLabel {
 public static function normalize($raw):string {
  if(!is_string($raw))throw new \InvalidArgumentException('Invalid catalogue label');
  $value=trim(strip_tags($raw));
  if($value==='' || strlen($value)>160 || preg_match('/[\x00-\x1f\x7f]/', $value))throw new \InvalidArgumentException('Invalid catalogue label');
  return $value;
 }
}
