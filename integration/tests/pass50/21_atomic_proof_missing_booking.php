<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>\Titan\BookingSidecar\BookingAtomicPersistProof::verify(reader50([]),27,command47(),'50.00'),'Non-unique or missing booking');
echo "PASS50 21_atomic_proof_missing_booking OK\n";
