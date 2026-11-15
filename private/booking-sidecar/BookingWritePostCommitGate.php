<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Verify persisted Cleanto records while the caller-owned transaction is still open. */
final class BookingWritePostCommitGate { public static function verify(\PDO $db,int $id,array $cmd,string $rate):void {[$booking,$order]=BookingWriteProofReader::read($db,$id);BookingWriteProofRows::check($booking,$order,$id,$cmd,$rate);} }
