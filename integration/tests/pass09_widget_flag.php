<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetPreviewFlag.php';
use App\Booking\Widget\WidgetPreviewFlag;
$r=WidgetPreviewFlag::mark(['booking_id'=>99,'reservable'=>true]);
assert($r['reservable']===false && !isset($r['booking_id']) && $r['status']==='preview_only');
echo 'widget_flag OK\n';
