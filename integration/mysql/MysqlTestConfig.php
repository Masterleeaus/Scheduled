<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class MysqlTestConfig {
 public static function fromEnvironment(array $env): array {
  $host=trim((string)($env['BOOKING_TEST_HOST']??'')); $db=trim((string)($env['BOOKING_TEST_DATABASE']??''));
  $user=(string)($env['BOOKING_TEST_USER']??'');$password=(string)($env['BOOKING_TEST_PASSWORD']??'');
  if($host===''||$db===''||$user==='')throw new \DomainException('Dedicated test database settings required');
  DisposableName::assert($db);if(!in_array($host,['127.0.0.1','localhost','mysql-test','mariadb-test'],true))throw new \DomainException('Unapproved test database host');
  return ['host'=>$host,'database'=>$db,'username'=>$user,'password'=>$password];
 }
}
