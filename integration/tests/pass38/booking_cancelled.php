<?php
declare(strict_types=1);
$base=dirname(__DIR__,3).'/core/app/Booking/Public/Redemption';
require_once $base."/CommitBookingId.php";
require_once $base."/CommitBookingOrderPolicy.php";
require_once $base."/CommitBookingStatusPolicy.php";
require_once $base."/CommitOrderDuration.php";
require_once $base."/CommitOrderRow.php";
require_once $base."/CommitOrderReader.php";
require_once $base."/CommitBookingIdentity.php";
require_once $base."/CommitCustomerOrder.php";
require_once $base."/CommitDurationProof.php";
require_once $base."/CommitPersistenceProof.php";
require_once $base."/CommitReplayOrderProof.php";
use App\Booking\Public\Redemption\CommitBookingId;
use App\Booking\Public\Redemption\CommitOrderDuration;
use App\Booking\Public\Redemption\CommitOrderRow;
use App\Booking\Public\Redemption\CommitOrderReader;
use App\Booking\Public\Redemption\CommitBookingIdentity;
use App\Booking\Public\Redemption\CommitCustomerOrder;
use App\Booking\Public\Redemption\CommitDurationProof;
use App\Booking\Public\Redemption\CommitPersistenceProof;
use App\Booking\Public\Redemption\CommitReplayOrderProof;
function throws(callable $f):bool{try{$f();return false;}catch(\Throwable){return true;}}
if(!(throws(fn()=>CommitBookingIdentity::validate(["id"=>"4","client_id"=>"6","order_id"=>"9","booking_status"=>"X"],4)))){fwrite(STDERR,"FAILED: booking_cancelled\n");exit(1);}echo "PASS booking_cancelled\n";
