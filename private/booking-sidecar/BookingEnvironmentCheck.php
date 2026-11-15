<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingEnvironmentCheck {
 public static function state(array $drivers):array {return ['mysql_driver'=>in_array('mysql',$drivers,true),'sqlite_driver'=>in_array('sqlite',$drivers,true),'write_enabled'=>false];}
}
