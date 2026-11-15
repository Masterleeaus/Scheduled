<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
expectFailure(fn()=>ConcurrencyProcessGroup::finishAll([[],[]],120001));
echo 'PASS 13_group_excessive_timeout\n';
