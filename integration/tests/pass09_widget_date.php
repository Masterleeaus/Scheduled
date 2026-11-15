<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetDateSelection.php';
use App\Booking\Widget\WidgetDateSelection;
assert(WidgetDateSelection::normalize('2026-11-09')==='2026-11-09');
try{WidgetDateSelection::normalize('2026-02-30');throw new Exception('invalid date');}catch(InvalidArgumentException $e){}
echo 'widget_date OK\n';
