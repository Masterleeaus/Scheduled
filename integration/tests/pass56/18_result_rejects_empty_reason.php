<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
expectFailure(fn()=>ConcurrencyResult::assertShape(['outcome'=>'rejected','reason'=>'']));expectFailure(fn()=>ConcurrencyResult::assertShape(['outcome'=>'rejected','reason'=>str_repeat('x',1025)]));
echo 'PASS 18_result_rejects_empty_reason\n';
