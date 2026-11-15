<?php
declare(strict_types=1);
ini_set('zend.assertions','1'); assert_options(ASSERT_ACTIVE,1); assert_options(ASSERT_EXCEPTION,1);
require_once __DIR__.'/../../core/app/Booking/Widget/WidgetPublicConfig.php';
use App\Booking\Widget\WidgetPublicConfig;
$r=WidgetPublicConfig::safe(['title'=>'Clean today','owner_id'=>2]);
assert(!isset($r['owner_id']) && $r['creates_booking']===false && $r['title']==='Clean today');
echo 'widget_config OK\n';
