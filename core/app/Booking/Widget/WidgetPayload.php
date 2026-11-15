<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Tenant identifiers are NEVER accepted from browser widget payloads. */
final class WidgetPayload { public static function parse(array $input): array { $service=WidgetServiceSelection::normalize($input['service_id']??null);$method=WidgetServiceSelection::normalize($input['method_id']??null);$unit=WidgetServiceSelection::normalize($input['method_unit_id']??null);$staff=WidgetStaffSelection::normalize($input['staff_ids']??null);$date=WidgetDateSelection::normalize($input['date']??null);$timezone=WidgetTimeZone::normalize($input['timezone']??null);$duration=WidgetDuration::normalize($input['duration_minutes']??null);$quantity=WidgetQuantity::normalize($input['quantity']??1);return ['date'=>$date,'timezone'=>$timezone,'staff_ids'=>$staff,'duration_minutes'=>$duration,'step_minutes'=>15,'service'=>['service_id'=>$service,'method_id'=>$method,'method_unit_id'=>$unit,'quantity'=>$quantity]];} }
