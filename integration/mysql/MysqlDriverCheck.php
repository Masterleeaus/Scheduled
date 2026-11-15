<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class MysqlDriverCheck {
 public static function available():bool{return extension_loaded('pdo_mysql')&&in_array('mysql',\PDO::getAvailableDrivers(),true);}
 public static function requireAvailable():void{if(!self::available())throw new \RuntimeException('pdo_mysql is required; integration test NOT RUN');}
}
