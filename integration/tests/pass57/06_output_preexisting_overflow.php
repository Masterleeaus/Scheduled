<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
expectFailure(fn()=>ConcurrencyProcessOutput::append(str_repeat('x',1025),'',1024));
echo 'PASS 06_output_preexisting_overflow\n';
