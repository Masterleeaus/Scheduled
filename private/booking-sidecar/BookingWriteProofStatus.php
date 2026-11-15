<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofStatus { public static function check(mixed $status):void {if($status!=='A')throw new \DomainException('New booking must be active');} }
