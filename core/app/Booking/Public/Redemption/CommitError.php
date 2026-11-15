<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitError { public static function safe(\Throwable $e):array {return ['state'=>'failed','confirmed'=>false,'payment_settled'=>false,'error'=>'booking_not_confirmed'];}}
