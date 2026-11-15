<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicAvailabilityError.php';
use App\Booking\Public\{PublicAvailabilityError};
assert(PublicAvailabilityError::status(new InvalidArgumentException('x'))===422);assert(PublicAvailabilityError::message(new RuntimeException('secret'))==='Availability currently unavailable');
echo "PASS errors\n";
