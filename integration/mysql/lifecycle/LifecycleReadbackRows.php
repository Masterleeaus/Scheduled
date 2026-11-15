<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Strictly require exactly one readback row after successful SQL; never trust a partial fetch. */
final class LifecycleReadbackRows {
 public static function exactlyOne(mixed $rows,string $context):array {
  if(!is_array($rows)||count($rows)!==1||!isset($rows[0])||!is_array($rows[0])||$rows[0]===[])throw new \RuntimeException('Missing or ambiguous '.$context.' readback');
  return $rows[0];
 }
}
