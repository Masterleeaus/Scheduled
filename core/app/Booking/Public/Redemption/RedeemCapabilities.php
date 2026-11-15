<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemCapabilities { public static function assertWriteReady(array $evidence):void {foreach(['verified_schema','verified_pricing','verified_staff','verified_availability','verified_tenant','verified_concurrency'] as $key){if(($evidence[$key]??null)!==true)throw new \RuntimeException('Booking write gate closed: '.$key);}} }
