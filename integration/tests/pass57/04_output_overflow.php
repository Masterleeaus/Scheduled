<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
expectFailure(fn()=>ConcurrencyProcessOutput::append(str_repeat('x',1024),'y',1024));
echo 'PASS 04_output_overflow\n';
