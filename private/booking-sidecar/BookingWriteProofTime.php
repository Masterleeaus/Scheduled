<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofTime { public static function check(mixed $stored,string $expected):void { if(!is_string($stored)||$stored!==$expected)throw new \DomainException('Persisted appointment start mismatch');} }
