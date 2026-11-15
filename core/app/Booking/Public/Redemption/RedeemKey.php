<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemKey { public static function fromNonce(string $nonce): string { if (!preg_match('/^[a-f0-9]{32}$/D',$nonce)) throw new \InvalidArgumentException('Invalid nonce'); return hash('sha256','titan-booking-intent:'.$nonce); } }
