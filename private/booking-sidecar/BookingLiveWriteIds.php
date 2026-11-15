<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Cleanto order_client_info.order_id is signed INT, not BIGINT. */
final class BookingLiveWriteIds {
 public const MAX_LEGACY_ORDER=2147483647;
 public static function order(int $id):int {if($id<1||$id>self::MAX_LEGACY_ORDER)throw new \DomainException('Cleanto legacy order ID overflow');return $id;}
 public static function foreignId(int $id):int {if($id<1||$id>2147483647)throw new \DomainException('Cleanto legacy identifier overflow');return $id;}
}
