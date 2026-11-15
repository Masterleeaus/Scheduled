<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$row=row50();$row['booking_status']='C';$row+=['service_id'=>1,'method_id'=>1,'method_unit_id'=>1,'method_unit_qty'=>'1','method_unit_qty_rate'=>'50.00'];$o=['id'=>32,'order_id'=>31,'client_name'=>'Test Customer','client_email'=>'test@example.com','client_phone'=>'0412345678','order_duration'=>60];$db=reader50([$row]);$s=new Pass50Statement;$s->data=[$o];$db->queue[]=$s;fail50(fn()=>\Titan\BookingSidecar\BookingAtomicPersistProof::verify($db,27,command47(),'50.00'),'New booking must be active');
echo "PASS50 23_atomic_proof_wrong_status OK\n";
