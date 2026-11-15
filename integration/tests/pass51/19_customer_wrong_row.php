<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingMutationCustomerScope(db51([['id'=>8]])))->assertBookingOwner(7,2),'does not own');
echo 'PASS51 19_customer_wrong_row OK';
