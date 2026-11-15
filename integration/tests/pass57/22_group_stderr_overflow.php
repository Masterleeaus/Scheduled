<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
function launchLocal(string $code):array {$pipes=[];$p=proc_open([PHP_BINARY,'-r',$code],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);if(!is_resource($p))throw new RuntimeException('Cannot launch local subprocess');fclose($pipes[0]);stream_set_blocking($pipes[1],false);stream_set_blocking($pipes[2],false);return [$p,$pipes];}
$h=[launchLocal('fwrite(STDERR,str_repeat("x",70000));'),launchLocal('usleep(10000);')];expectFailure(fn()=>ConcurrencyProcessGroup::finishAll($h,2000));
echo 'PASS 22_group_stderr_overflow\n';
