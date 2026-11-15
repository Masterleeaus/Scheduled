<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Run workers in independent PDO processes, bounded by a hard wall-clock deadline. */
final class ConcurrencyProcess {
 public static function launch(string $worker,string $dir,string $fixturePath,array $env):array {
  ConcurrencyProtocol::worker($worker);ConcurrencyProtocol::directory($dir);
  if(!is_file($fixturePath)||!is_readable($fixturePath))throw new \InvalidArgumentException('Fixture unavailable');
  $cmd=[PHP_BINARY,__DIR__.'/concurrent_worker.php',$worker,$dir,$fixturePath];
  $pipes=[];$proc=proc_open($cmd,[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes,null,$env);
  if(!is_resource($proc))throw new \RuntimeException('Cannot start worker');fclose($pipes[0]);
  stream_set_blocking($pipes[1],false);stream_set_blocking($pipes[2],false);
  return [$proc,$pipes];
 }
 public static function finish(array $handle,int $timeoutMs=30000):int {
  [$proc,$pipes]=$handle;$start=microtime(true);$stdout='';$stderr='';$exit=null;
  try {
   do {
    $stdout=ConcurrencyProcessOutput::append($stdout,(string)stream_get_contents($pipes[1]));
    $stderr=ConcurrencyProcessOutput::append($stderr,(string)stream_get_contents($pipes[2]));
    $status=proc_get_status($proc);
    if(!$status['running']){$exit=(int)$status['exitcode'];break;}
    if(ConcurrencyDeadline::expired($start,$timeoutMs,microtime(true)))throw new \RuntimeException('Concurrency worker deadline exceeded');
    usleep(20000);
   }while(true);
   $stderr=ConcurrencyProcessOutput::append($stderr,(string)stream_get_contents($pipes[2]));
   ConcurrencyWorkerExit::assert($exit,$stderr);
   return 0;
  }finally {
   if(is_resource($proc)){$s=proc_get_status($proc);if($s['running'])proc_terminate($proc);}
   foreach($pipes as $pipe)if(is_resource($pipe))fclose($pipe);
   if(is_resource($proc))proc_close($proc);
  }
 }
 public static function terminate(array $handle):void {[$proc,$pipes]=$handle;
  if(is_resource($proc)){$s=proc_get_status($proc);if($s['running'])proc_terminate($proc);}
  foreach($pipes as $pipe)if(is_resource($pipe))fclose($pipe);if(is_resource($proc))proc_close($proc);
 }
}
