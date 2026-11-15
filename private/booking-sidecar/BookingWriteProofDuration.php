<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofDuration { public static function check(mixed $stored,int $expected):void {if(!is_numeric($stored)||!preg_match('/^[0-9]+$/D',(string)$stored)||(int)$stored!==$expected)throw new \DomainException('Order duration mismatch');} }
