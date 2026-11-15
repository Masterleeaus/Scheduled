<?php
declare(strict_types=1);
spl_autoload_register(function(string $name):void{$prefix='App\\Booking\\Public\\Customer\\';if(str_starts_with($name,$prefix)){$file=__DIR__.'/../../../core/app/Booking/Public/Customer/'.substr($name,strlen($prefix)).'.php';if(is_file($file))require $file;}});
use App\Booking\Public\Customer\{CustomerBookingGrant,CustomerBookingLookup,CustomerBookingProjection,CustomerBookingToken,CustomerBookingHeaders,CustomerBookingStatus,CustomerBookingGrantIssuer,CustomerBookingReadback};
$count=0;
function ok69(bool $truth,string $label):void{global $count;if(!$truth)throw new RuntimeException($label);$count++;}
function bad69(callable $fn,string $label):void{global $count;try{$fn();}catch(Throwable $e){$count++;return;}throw new RuntimeException('Expected rejection: '.$label);}
$now=1800000000;$secret=str_repeat('z',32);$token=CustomerBookingGrant::issue(7,21,$secret,$now);
$row=['id'=>21,'tenant_id'=>7,'reference'=>'BK-TEST-00021','status'=>'confirmed'];
$read=fn($tenant,$booking)=>$row;
$result=CustomerBookingLookup::read($token,7,$now,$secret,$read);
ok69($result['confirmed']===true,'confirmed from persisted row');ok69($result['source']==='persisted_booking','persisted source');ok69($result['payment_settled']===false,'no false payment claim');
ok69(CustomerBookingGrant::verify($token,7,$now,$secret)===21,'verified booking identity');
bad69(fn()=>CustomerBookingGrant::verify($token,8,$now,$secret),'tenant mismatch');
bad69(fn()=>CustomerBookingGrant::verify($token,7,$now+900,$secret),'expired');
bad69(fn()=>CustomerBookingGrant::verify($token,7,$now-1,$secret),'not yet valid');
bad69(fn()=>CustomerBookingGrant::verify($token,7,$now,str_repeat('y',32)),'wrong key');
bad69(fn()=>CustomerBookingGrant::verify(substr($token,0,-1).'0',7,$now,$secret),'tampered');
bad69(fn()=>CustomerBookingGrant::verify('a.b',7,$now,$secret),'malformed');
bad69(fn()=>CustomerBookingGrant::issue(0,21,$secret,$now),'zero tenant');
bad69(fn()=>CustomerBookingGrant::issue(7,0,$secret,$now),'zero booking');
bad69(fn()=>CustomerBookingGrant::issue(7,21,'short',$now),'short secret');
bad69(fn()=>CustomerBookingToken::decode(str_repeat('z',1300),$secret),'oversized');
bad69(fn()=>CustomerBookingLookup::read($token,7,$now,$secret,fn()=>null),'missing persisted booking');
bad69(fn()=>CustomerBookingLookup::read($token,7,$now,$secret,fn()=>['id'=>21,'tenant_id'=>8,'reference'=>'BK-TEST-00021','status'=>'confirmed']),'wrong persisted tenant');
bad69(fn()=>CustomerBookingLookup::read($token,7,$now,$secret,fn()=>['id'=>22,'tenant_id'=>7,'reference'=>'BK-TEST-00021','status'=>'confirmed']),'wrong persisted booking');
bad69(fn()=>CustomerBookingProjection::fromPersisted(['id'=>21,'tenant_id'=>7,'reference'=>'bad reference','status'=>'confirmed'],7,21),'invalid reference');
bad69(fn()=>CustomerBookingProjection::fromPersisted(['id'=>21,'tenant_id'=>7,'reference'=>'BK-TEST-00021','status'=>'unknown'],7,21),'unknown status');
foreach(['pending_payment','payment_processing','cancelled','completed','refunded'] as $status){$r=CustomerBookingProjection::fromPersisted($row+[],7,21);$x=$row;$x['status']=$status;$p=CustomerBookingProjection::fromPersisted($x,7,21);ok69($p['confirmed']===false && $p['can_manage']===false,'nonconfirmed '.$status);}
ok69(CustomerBookingHeaders::all()['Cache-Control']==='no-store, private','private no-cache');
$granted=CustomerBookingGrantIssuer::afterReadback(7,21,$now,$secret,fn()=> $row);
ok69(CustomerBookingGrant::verify($granted,7,$now,$secret)===21,'issue only after readback');
bad69(fn()=>CustomerBookingGrantIssuer::afterReadback(7,21,$now,$secret,fn()=> null),'no readback grant');
bad69(fn()=>CustomerBookingGrantIssuer::afterReadback(7,21,$now,$secret,fn()=> ['id'=>21,'tenant_id'=>9,'reference'=>'BK-TEST-00021','status'=>'confirmed']),'cross-tenant grant');

echo 'Pass 69 customer lookup assertions: '.$count." passed\n";
