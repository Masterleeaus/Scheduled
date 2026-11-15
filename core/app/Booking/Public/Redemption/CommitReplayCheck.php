<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitReplayCheck { public static function resolve(array $row,int $owner,string $fingerprint,callable $read):array {if(CommitLedgerState::validate($row,$owner,$fingerprint)!=='committed')throw new \RuntimeException('Claim is not committed');$id=CommitBookingId::parse($row['booking_id']??null);CommitEvidence::assert($read($id),$id);return CommitResponse::confirmed($id,true);}}
