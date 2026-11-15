<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetTimeZone.php';
use App\Booking\Widget\WidgetTimeZone;
assert(WidgetTimeZone::normalize('Australia/Melbourne')==='Australia/Melbourne');
try{WidgetTimeZone::normalize('invalid timezone');throw new Exception('invalid tz');}catch(InvalidArgumentException $e){}
echo 'widget_timezone OK\n';
