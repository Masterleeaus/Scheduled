<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
expectFailure(fn()=>ConcurrencyProcess::launch('a','/tmp/not-an-acceptance-run','/tmp/no-fixture',[]));
echo 'PASS 24_process_invalid_directory\n';
