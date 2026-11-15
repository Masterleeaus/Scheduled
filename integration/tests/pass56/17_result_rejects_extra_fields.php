<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
expectFailure(fn()=>ConcurrencyResult::assertShape(['outcome'=>'accepted','booking_id'=>1,'admin'=>true]));expectFailure(fn()=>ConcurrencyResult::assertShape(['outcome'=>'rejected','reason'=>'conflict','booking_id'=>1]));
echo 'PASS 17_result_rejects_extra_fields\n';
