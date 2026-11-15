<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitEvidence { public static function assert(array $row,int $bookingId):void { if(CommitBookingId::parse($row['id']??null)!==$bookingId)throw new \RuntimeException('Persisted booking mismatch');if(!isset($row['client_id']) || CommitBookingId::parse($row['client_id'])<1)throw new \RuntimeException('Missing persisted customer');if(!is_string($row['booking_date_time']??null)||trim($row['booking_date_time'])==='')throw new \RuntimeException('Missing persisted appointment time');CommitBookingStatusPolicy::requireActive($row['booking_status']??null);}}
