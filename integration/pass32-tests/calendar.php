<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicDay.php';
use App\Booking\Public\{PublicDay};
$today=(new DateTimeImmutable('today',new DateTimeZone('Australia/Melbourne')))->format('Y-m-d');assert(PublicDay::parse($today,'Australia/Melbourne')===$today);try{PublicDay::parse('2026-02-30','Australia/Melbourne');throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "PASS calendar\n";
