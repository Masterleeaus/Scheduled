<?php
$source=file_get_contents(__DIR__.'/../../core/app/Http/Controllers/Front/BookingPublicAvailabilityController.php');if(strpos($source,'$request->getHost()')===false)throw new RuntimeException('Missing controller-host contract');echo 'PASS controller-host\n';
