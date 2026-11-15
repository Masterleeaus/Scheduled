<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemGuard { public static function authorize(array $evidence,bool $featureEnabled):void {if(!$featureEnabled)throw new \RuntimeException('Public booking write disabled');RedeemCapabilities::assertWriteReady($evidence);} }
