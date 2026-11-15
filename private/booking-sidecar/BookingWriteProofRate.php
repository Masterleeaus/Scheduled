<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofRate { public static function check(mixed $stored,string $expected):void {if(!is_string($stored)&&!is_numeric($stored))throw new \DomainException('Missing persisted rate');if(BookingMoney::cents((string)$stored)!==BookingMoney::cents($expected))throw new \DomainException('Persisted rate mismatch');} }
