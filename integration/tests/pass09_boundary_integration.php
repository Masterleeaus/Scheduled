<?php
$root=dirname(__DIR__,2);
$routes=file_get_contents($root.'/core/routes/web.php');
if(strpos($routes,"Route::post('/booking/preview'")===false)throw new RuntimeException('Owner route missing');
if(strpos($routes,"'middleware' => ['auth', 'userstatus', 'setlang']")===false)throw new RuntimeException('Owner middleware missing');
$client=file_get_contents($root.'/core/app/Booking/InternalBookingClient.php');
if(strpos($client,"'booking_preview'")===false)throw new RuntimeException('Preview allowlist missing');
if(strpos($client,"'create_booking'")!==false)throw new RuntimeException('Writes unexpectedly enabled');
$view=file_get_contents($root.'/core/resources/views/admin/pagebuilder/content.blade.php');
if(strpos($view,"blockManager.add('titan-booking-preview'")===false)throw new RuntimeException('Builder block missing');
echo "Preview boundaries retained\n";
