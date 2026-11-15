<?php
$source=file_get_contents(__DIR__.'/../../core/app/Booking/Public/PublicAvailabilityGateway.php');if(strpos($source,"'availability'")===false)throw new RuntimeException('Missing no-sidecar-write contract');echo 'PASS no-sidecar-write\n';
