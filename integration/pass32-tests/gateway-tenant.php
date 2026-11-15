<?php
$source=file_get_contents(__DIR__.'/../../core/app/Booking/Public/PublicAvailabilityGateway.php');if(strpos($source,'$this->registry->forOwner($owner)')===false)throw new RuntimeException('Missing gateway-tenant contract');echo 'PASS gateway-tenant\n';
