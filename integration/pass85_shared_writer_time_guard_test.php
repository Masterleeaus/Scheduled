<?php
declare(strict_types=1);
/** Pass 85 public redemption time-of-write contract regression. */
$source = file_get_contents(__DIR__ . '/../private/booking-sidecar/BookingSharedTransactionWriter.php');
if (!is_string($source)) {fwrite(STDERR,"Cannot load writer\n"); exit(1);}
$checks = [
 'normalization remains first' => strpos($source, 'BookingSharedCommand::normalize($payload)') !== false,
 'tenant checks retained' => strpos($source, 'BookingSharedTransactionScope::assert($db,$this->tenant)') !== false,
 'rate authoritative' => strpos($source, 'BookingSharedRate::authoritative($db,$command,$this->tenant)') !== false,
 'availability authoritative' => strpos($source, 'BookingSharedAvailability::assert($db,$command)') !== false,
 'final time guard exists' => substr_count($source, "BookingMutationTimeGuard::assertFuture(\$command['window'],time())") === 1,
 'time guard after availability' => strpos($source, "BookingMutationTimeGuard::assertFuture(\$command['window'],time())") > strpos($source, 'BookingSharedAvailability::assert($db,$command)'),
 'time guard before persistence' => strpos($source, "BookingMutationTimeGuard::assertFuture(\$command['window'],time())") < strpos($source, 'BookingSharedPersist::write($db,$command,$rate)'),
 'postwrite proof retained' => strpos($source, 'BookingWritePostCommitGate::verify($db,$id,$command,$rate)') !== false,
 'transaction never commits' => !preg_match('/\$db->(?:commit|beginTransaction|rollBack)\s*\(/', $source),
];
foreach($checks as $label=>$ok){ if(!$ok){fwrite(STDERR,"FAIL $label\n");exit(1);} echo "PASS $label\n"; }
