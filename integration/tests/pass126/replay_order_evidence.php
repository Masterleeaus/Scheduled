<?php
declare(strict_types=1);
// Executable source-level test using PDO-compatible in-memory results, not live MariaDB acceptance.
spl_autoload_register(static function(string $class):void {
    $prefix='Titan\\BookingSidecar\\';
    if (str_starts_with($class,$prefix)) {
        $name=substr($class,strlen($prefix));
        $file=dirname(__DIR__,3).'/private/booking-sidecar/'.$name.'.php';
        if (is_file($file)) require $file;
    }
});
use Titan\BookingSidecar\BookingCreationReplayProof;
use Titan\BookingSidecar\BookingPersonalInfo;
final class ReplayRowsStatement extends PDOStatement {
    public array $rows=[];
    public function execute(?array $params=null):bool {return true;}
    public function fetch(int $mode=PDO::FETCH_DEFAULT,int $cursorOrientation=PDO::FETCH_ORI_NEXT,int $cursorOffset=0):mixed {return array_shift($this->rows)??false;}
}
final class ReplayRowsPdo extends PDO {
    public array $booking=[];
    public array $customer=[];
    public function __construct(){}
    public function inTransaction():bool{return true;}
    public function prepare(string $query,array $options=[]):PDOStatement|false {
        $s=new ReplayRowsStatement();
        $s->rows=[str_contains($query,'FROM ct_bookings')?$this->booking:$this->customer];
        return $s;
    }
}
$cmd=[
    'window'=>['local_start'=>'2026-11-24 09:00:00'],
    'service'=>['service_id'=>2,'method_id'=>3,'method_unit_id'=>4,'quantity'=>1],
    'staff_ids'=>[6], 'duration_minutes'=>60,
    'customer'=>['name'=>'Alex Test','email'=>'alex@example.test','phone'=>'0412345678'],
    'personal_info'=>['zip'=>'3000','address'=>'14 Sample St','city'=>'Melbourne','state'=>'VIC','notes'=>'Gate access'],
];
$db=new ReplayRowsPdo();
$db->booking=['id'=>'21','order_id'=>'108','client_id'=>'35','order_date'=>'2026-11-24','booking_date_time'=>'2026-11-24 09:00:00','service_id'=>'2','method_id'=>'3','method_unit_id'=>'4','method_unit_qty'=>'1','method_unit_qty_rate'=>'130.00','booking_status'=>'A','staff_ids'=>'6'];
$db->customer=['id'=>'35','order_id'=>'108','client_name'=>'Alex Test','client_email'=>'alex@example.test','client_phone'=>'0412345678','client_personal_info'=>BookingPersonalInfo::encode($cmd['personal_info']),'order_duration'=>'60'];
$assertions=0;
$check=static function(bool $value,string $message)use(&$assertions):void{ $assertions++;if(!$value)throw new RuntimeException($message);};
$check(BookingCreationReplayProof::verifiedOrderId($db,21,$cmd)===108,'Replay should return verified persisted order ID');
$reject=static function(callable $fn,string $message) use ($check):void {try{$fn();}catch(DomainException $e){$check(true,$message);return;} $check(false,$message);};
foreach ([
    ['booking','order_id','109'], ['booking','client_id','36'], ['booking','booking_status','C'],
    ['booking','order_date','2026-11-25'], ['customer','order_id','109'], ['customer','client_email','wrong@example.test'],
    ['customer','client_personal_info','corrupt'], ['booking','staff_ids','7'],
] as [$kind,$column,$bad]) {
    $original=$db->{$kind}[$column]; $db->{$kind}[$column]=$bad;
    $reject(static fn()=>BookingCreationReplayProof::verifiedOrderId($db,21,$cmd),"Invalid $kind.$column must reject replay");
    $db->{$kind}[$column]=$original;
}
$check(BookingCreationReplayProof::verifiedOrderId($db,21,$cmd)===108,'Proof should survive rejected alterations');
echo "PASS126: $assertions replay evidence assertions passed (PDO result test double, no live database)\n";
