<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicStaffId.php';
require_once __DIR__.'/../../core/app/Booking/Public/PublicDuration.php';
require_once __DIR__.'/../../core/app/Booking/Public/PublicTimezone.php';
require_once __DIR__.'/../../core/app/Booking/Public/PublicDay.php';
require_once __DIR__.'/../../core/app/Booking/Public/PublicAvailabilityInput.php';
use App\Booking\Public\{PublicStaffId,PublicDuration,PublicTimezone,PublicDay,PublicAvailabilityInput};
$today=(new DateTimeImmutable('today',new DateTimeZone('Australia/Melbourne')))->format('Y-m-d');$r=PublicAvailabilityInput::parse(['staff_id'=>'1','date'=>$today,'duration_minutes'=>'60']);assert($r['step_minutes']===15);try{PublicAvailabilityInput::parse(['staff_id'=>1,'date'=>$today,'duration_minutes'=>60,'tenant_id'=>3]);throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "PASS input\n";
