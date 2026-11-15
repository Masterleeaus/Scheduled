<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\ConcurrencyProcessGroup;
function spawn81(string $code):array {
 $pipes=[];$p=proc_open([PHP_BINARY,'-r',$code],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);
 if(!is_resource($p))throw new RuntimeException('Could not spawn isolated test worker');
 fclose($pipes[0]);stream_set_blocking($pipes[1],false);stream_set_blocking($pipes[2],false);return [$p,$pipes];
}
function stop81(array $h):void {if(is_resource($h[0])){$s=proc_get_status($h[0]);if($s['running'])proc_terminate($h[0]);}foreach($h[1] as $p)if(is_resource($p))fclose($p);if(is_resource($h[0]))proc_close($h[0]);}
function failure81(callable $run,string $expected):void {$failed=false;try{$run();}catch(Throwable $e){$failed=str_contains($e->getMessage(),$expected);}check($failed);}
// Genuine OS subprocesses, without pretending to execute database booking writes.
ConcurrencyProcessGroup::finishAll([spawn81('usleep(30000);exit(0);'),spawn81('usleep(70000);exit(0);')],3000);
failure81(fn()=>ConcurrencyProcessGroup::finishAll([spawn81('exit(7);'),spawn81('usleep(300000);exit(0);')],2000),'exit 7');
failure81(fn()=>ConcurrencyProcessGroup::finishAll([spawn81('usleep(800000);exit(0);'),spawn81('usleep(800000);exit(0);')],120),'deadline exceeded');
failure81(fn()=>ConcurrencyProcessGroup::finishAll([spawn81('fwrite(STDERR,str_repeat("x",70000));exit(0);'),spawn81('usleep(100000);exit(0);')],3000),'output exceeded safety limit');
$one=spawn81('exit(0);');
failure81(fn()=>ConcurrencyProcessGroup::finishAll([$one,$one],1000),'Duplicate concurrent worker handle');stop81($one);
check(true);echo "PASS pass81_worker_runtime: 5 subprocess scenarios\n";
