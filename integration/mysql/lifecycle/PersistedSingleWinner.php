<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedSingleWinner {
 public static function assert(array $ids,int $winner):void {
  if(count($ids)!==1 || $ids[0]!==$winner)throw new \RuntimeException('Persisted overlapping booking count not exactly one winner');
 }
}
