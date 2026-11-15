<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
expectFailure(fn()=>ConcurrencyProtocol::directory(sys_get_temp_dir()));
echo 'PASS 02_directory_rejects_arbitrary\n';
