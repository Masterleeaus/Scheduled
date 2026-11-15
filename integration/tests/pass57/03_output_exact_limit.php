<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
check(strlen(ConcurrencyProcessOutput::append(str_repeat('x',1023),'y',1024))===1024);
echo 'PASS 03_output_exact_limit\n';
