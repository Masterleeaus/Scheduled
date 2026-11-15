<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Authenticated actor identity must be injected by private server gateway. */
final class BookingMutationActor {
 public static function normalize(array $actor):array {
  $role=$actor['role']??null;$id=$actor['id']??null;
  if(!in_array($role,['owner','customer'],true)||!is_int($id)||$id<1||$id>2147483647)throw new \InvalidArgumentException('Invalid booking actor');
  return ['role'=>$role,'id'=>$id];
 }
}
