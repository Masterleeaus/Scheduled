<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
$p=proc_open([PHP_BINARY,'-r','exit(0);'],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);try{expectFailure(fn()=>ConcurrencyProcessGroup::finishAll([[$p,[]],[$p,$pipes]]));}finally{foreach($pipes as $pipe)if(is_resource($pipe))fclose($pipe);if(is_resource($p))proc_close($p);}
echo 'PASS 16_group_bad_pipes\n';
