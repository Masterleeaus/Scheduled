<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitReleaseGate { public static function require(array $evidence,bool $enabled):void { RedeemGuard::authorize($evidence,$enabled); if(($evidence['same_database_transaction']??null)!==true ||($evidence['independent_persisted_readback']??null)!==true)throw new \RuntimeException('Booking commit evidence not proven');}}
