<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicStaffMembership.php';
use App\Booking\Public\{PublicStaffMembership};
PublicStaffMembership::assertMember(['preview_only'=>true,'booking_writes_enabled'=>false,'staff'=>[['id'=>4,'label'=>'Alex']]],4);try{PublicStaffMembership::assertMember(['preview_only'=>true,'booking_writes_enabled'=>false,'staff'=>[['id'=>4]]],5);throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "PASS membership\n";
