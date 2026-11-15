<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicDuration.php';
use App\Booking\Public\{PublicDuration};
assert(PublicDuration::parse('120')===120);try{PublicDuration::parse(31);throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "PASS duration\n";
