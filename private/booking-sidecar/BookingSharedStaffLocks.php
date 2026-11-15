<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Lock names include the tenant database, not the public hostname. */
final class BookingSharedStaffLocks {
 public static function keys(\PDO $db,array $selection):array {
  if(!is_int($selection['staff_id']??null)||$selection['staff_id']<1||$selection['staff_id']>2147483647)throw new \InvalidArgumentException('Invalid selected staff');
  return BookingTenantMutationLocks::creation($db,[$selection['staff_id']]);
 }
}
