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
if(!((CommitDurationProof::validate(["order_id"=>"9"],["order_id"=>"9","order_duration"=>"60","client_name"=>"Jane","client_email"=>"a@b.co","client_phone"=>"123"],["duration_minutes"=>60])===null))){fwrite(STDERR,"FAILED: duration_proof\n");exit(1);}echo "PASS duration_proof\n";
