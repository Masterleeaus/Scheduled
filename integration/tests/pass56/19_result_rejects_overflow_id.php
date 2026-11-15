<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
expectFailure(fn()=>ConcurrencyResult::accepted(2147483648));expectFailure(fn()=>ConcurrencyResult::assertShape(['outcome'=>'accepted','booking_id'=>2147483648]));
echo 'PASS 19_result_rejects_overflow_id\n';
