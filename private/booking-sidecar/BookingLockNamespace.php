<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Canonical twelve-character namespace derived from the selected tenant database. */
final class BookingLockNamespace {
 public static function fromDatabase(string $name):string {return substr(hash('sha256',BookingDatabaseIdentity::validate($name)),0,12);}
 public static function validate(string $value):string {if(!preg_match('/^[a-f0-9]{12}$/D',$value))throw new \InvalidArgumentException('Invalid tenant lock namespace');return $value;}
}
