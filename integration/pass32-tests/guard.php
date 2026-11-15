<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicAvailabilityGuard.php';
use App\Booking\Public\{PublicAvailabilityGuard};
PublicAvailabilityGuard::assertReadOnly('POST',['staff_id'=>1]);try{PublicAvailabilityGuard::assertReadOnly('POST',['payment'=>true]);throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "PASS guard\n";
