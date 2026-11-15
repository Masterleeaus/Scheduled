<?php
declare(strict_types=1);namespace App\Booking\Public\Intent;
final class IntentResponse{public static function preview(string $token):array{return ['intent_token'=>$token,'preview_only'=>true,'booking_writes_enabled'=>false,'confirmed'=>false,'payment_settled'=>false];}}
