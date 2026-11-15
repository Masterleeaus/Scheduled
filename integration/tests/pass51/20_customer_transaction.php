<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingMutationCustomerScope(db51([],true,1,false)))->assertBookingOwner(7,2),'transaction');
echo 'PASS51 20_customer_transaction OK';
