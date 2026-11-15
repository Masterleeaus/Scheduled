<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetPreviewFlag { public static function mark(array $result):array { $result['reservable']=false;$result['status']='preview_only';$result['requires_final_availability_recheck']=true;unset($result['booking_id'],$result['confirmation_id']);return $result; } }
