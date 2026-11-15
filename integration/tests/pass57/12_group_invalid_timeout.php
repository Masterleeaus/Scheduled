<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
expectFailure(fn()=>ConcurrencyProcessGroup::finishAll([[],[]],99));
echo 'PASS 12_group_invalid_timeout\n';
