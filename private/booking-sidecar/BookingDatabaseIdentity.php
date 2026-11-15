<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** One tenant DB per PDO connection; report redacted identifiers only. */
final class BookingDatabaseIdentity {
 public static function validate(string $database):string {
  if(!preg_match('/^[a-zA-Z0-9_]{1,64}$/D',$database))throw new \InvalidArgumentException('Unsafe database name');
  return $database;
 }
 public static function safeReference(string $database):string {return substr(hash('sha256',self::validate($database)),0,12);}
}
