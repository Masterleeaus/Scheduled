<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetStaffSelection.php';
use App\Booking\Widget\WidgetStaffSelection;
assert(WidgetStaffSelection::normalize(['7','2'])===[2,7]);
try{WidgetStaffSelection::normalize(['2','2']);throw new Exception('duplicates');}catch(InvalidArgumentException $e){}
echo 'widget_staff OK\n';
