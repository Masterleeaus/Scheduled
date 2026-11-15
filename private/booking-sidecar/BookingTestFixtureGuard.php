<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Destructive integration tests require an explicitly marked disposable database. */
final class BookingTestFixtureGuard {
 public static function assertDisposable(array $config):void {
  if(($config['allow_destructive_test']??false)!==true)throw new \RuntimeException('Destructive test not explicitly permitted');
  $name=BookingDatabaseIdentity::validate((string)($config['database']??''));
  if(!str_starts_with($name,'test_titan_booking_'))throw new \RuntimeException('Refusing non-test database');
 }
}
