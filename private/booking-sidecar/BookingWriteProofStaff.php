<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofStaff { public static function check(mixed $stored,array $expected):void { if(!is_string($stored)||!preg_match('/^[1-9][0-9]*(,[1-9][0-9]*)*$/D',$stored))throw new \DomainException('Invalid stored staff');$ids=BookingStrictStaffIds::parse($stored);sort($ids,SORT_NUMERIC);$want=$expected;sort($want,SORT_NUMERIC);if($ids!==$want)throw new \DomainException('Staff mismatch');} }
