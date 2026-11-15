<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetQuantity.php';
use App\Booking\Widget\WidgetQuantity;
assert(WidgetQuantity::normalize('4')===4);
try{WidgetQuantity::normalize('101');throw new Exception('bad quantity');}catch(InvalidArgumentException $e){}
echo 'widget_quantity OK\n';
