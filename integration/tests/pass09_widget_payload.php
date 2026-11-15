<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetServiceSelection.php';
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetStaffSelection.php';
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetDateSelection.php';
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetTimeZone.php';
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetDuration.php';
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetQuantity.php';
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetPayload.php';
use App\Booking\Widget\WidgetPayload;
$r=WidgetPayload::parse(['service_id'=>7,'method_id'=>9,'method_unit_id'=>3,'quantity'=>2,'staff_ids'=>['7','2'],'date'=>'2026-11-09','timezone'=>'Australia/Melbourne','duration_minutes'=>60]);
assert($r['service']['service_id']===7 && $r['service']['method_id']===9 && $r['staff_ids']===[2,7] && !isset($r['tenant_id']));
echo 'widget_payload OK\n';
