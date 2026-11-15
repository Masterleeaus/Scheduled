<?php
declare(strict_types=1);
$base=dirname(__DIR__,3);
$view=file_get_contents($base.'/core/resources/views/front/partials/titan-booking-widget.blade.php');
$assets=file_get_contents($base.'/core/resources/views/front/partials/titan-booking-assets.blade.php');
$script=file_get_contents($base.'/assets/front/js/titan-booking-widget.js');
$routes=file_get_contents($base.'/core/routes/web.php');
$checks=[
 'preview disclaimer'=>str_contains($view,'Not a reservation'),
 'no confirmed CTA'=>!str_contains($view,'Confirm booking'),
 'catalogue route'=>str_contains($routes,"'/titan-booking/catalogue'"),
 'availability route'=>str_contains($routes,"'/titan-booking/availability'"),
 'shared read-only validator loaded first'=>strpos($assets,'titan-booking-preview-model.js')<strpos($assets,'titan-booking-catalogue.js'),
 'widget script loaded after catalogue'=>strpos($assets,'titan-booking-catalogue.js')<strpos($assets,'titan-booking-widget.js'),
 'availability read-only safety check'=>str_contains($script,'model.slots(data)'),
 'request race cancellation'=>str_contains($script,'current!==sequence'),
 'submission requires server capability'=>str_contains($script,"root.dataset.bookingSubmitEnabled!=='true'"),
 'csrf transport imported'=>str_contains($assets,'titan-booking-transport.js'),
 'status is live region'=>str_contains($view,'aria-live="polite"'),
];
foreach($checks as $name=>$success){echo ($success?'PASS':'FAIL').' '.$name.PHP_EOL;if(!$success)exit(1);}echo count($checks).' integration contract checks passed'.PHP_EOL;
