<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Owner IDs are server-side claims from the tenant secret registry, never from customer payload. */
final class BookingOwnerIdentity {
 public static function fromTenantConfig(array $settings):array {
  $id=$settings['authenticated_owner_id']??null;
  if(!is_int($id)||$id<1)throw new \DomainException('Authenticated business owner not configured');
  return ['role'=>'owner','id'=>$id];
 }
 public static function assertMatches(array $actor,array $settings):void {
  $expected=self::fromTenantConfig($settings);
  if(BookingMutationActor::normalize($actor)!==$expected)throw new \DomainException('Owner identity does not match tenant configuration');
 }
}
