<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetPublicConfig { public static function safe(array $data):array {return ['title'=>substr(trim((string)($data['title']??'Check availability')),0,100),'preview_only'=>true,'accepts_payment'=>false,'creates_booking'=>false];} }
