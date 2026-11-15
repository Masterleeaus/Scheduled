<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>\Titan\BookingSidecar\BookingAtomicPersistProof::verify(reader50([row50()],true,false),27,command47(),'50.00'),'closed caller-owned');
echo "PASS50 20_atomic_proof_outside_transaction OK\n";
