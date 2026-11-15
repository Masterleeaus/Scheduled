<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicTimezone.php';
use App\Booking\Public\{PublicTimezone};
assert(PublicTimezone::parse('Australia/Melbourne')==='Australia/Melbourne');try{PublicTimezone::parse('Fake/Timezone');throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "PASS timezone\n";
