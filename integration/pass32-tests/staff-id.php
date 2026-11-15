<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicStaffId.php';
use App\Booking\Public\{PublicStaffId};
assert(PublicStaffId::parse('14')===14);try{PublicStaffId::parse('0');throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "PASS staff-id\n";
