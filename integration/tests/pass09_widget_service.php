<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetServiceSelection.php';
use App\Booking\Widget\WidgetServiceSelection;
assert(WidgetServiceSelection::normalize('12')===12);
try{WidgetServiceSelection::normalize('0');throw new Exception('accepted zero');}catch(InvalidArgumentException $e){}
echo 'widget_service OK\n';
