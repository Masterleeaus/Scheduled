<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetDuration.php';
use App\Booking\Widget\WidgetDuration;
assert(WidgetDuration::normalize(60)===60);
try{WidgetDuration::normalize(17);throw new Exception('bad duration');}catch(InvalidArgumentException $e){}
echo 'widget_duration OK\n';
