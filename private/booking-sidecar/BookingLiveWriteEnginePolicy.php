<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** All tables participating in atomic redemption must be transactional. */
final class BookingLiveWriteEnginePolicy {
 public static function errors(array $engines):array {$errors=[];foreach(array_keys(BookingLiveWriteColumns::required()) as $table){if(strtoupper((string)($engines[$table]??''))!=='INNODB')$errors[]='non_transactional:'.$table;}return $errors;}
}
