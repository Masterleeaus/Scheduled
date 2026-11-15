<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemOutcome { public static function preview():array {return ['booking_writes_enabled'=>false,'confirmed'=>false,'payment_settled'=>false,'state'=>'preview_only'];} public static function reject(string $code):array {if(!preg_match('/^[a-z_]{3,40}$/D',$code))throw new \InvalidArgumentException('Invalid rejection');return ['booking_writes_enabled'=>false,'confirmed'=>false,'payment_settled'=>false,'error'=>$code];} }
