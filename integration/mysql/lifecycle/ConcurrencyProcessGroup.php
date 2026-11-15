<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Wait for both actual workers concurrently, bounded by one deadline. */
final class ConcurrencyProcessGroup {
 public static function finishAll(array $handles,int $timeoutMs=30000):void {
  if(count($handles)!==2||$timeoutMs<100||$timeoutMs>120000)throw new \InvalidArgumentException('Two workers and a bounded deadline required');
  $state=[];$seen=[];
  foreach($handles as $key=>$h){
   if(!is_array($h)||count($h)!==2||!isset($h[0],$h[1])||!is_resource($h[0])||!is_array($h[1])||!isset($h[1][1],$h[1][2])||!is_resource($h[1][1])||!is_resource($h[1][2]))throw new \InvalidArgumentException('Invalid concurrent process handle');
   $pid=get_resource_id($h[0]);
   if(isset($seen[$pid]))throw new \InvalidArgumentException('Duplicate concurrent worker handle');
   $seen[$pid]=true;
   $state[$key]=['proc'=>$h[0],'pipes'=>$h[1],'out'=>'','err'=>'','exit'=>null];
  }
  $start=microtime(true);
  try {
   while(true){$done=0;
    foreach($state as &$s){
     $s['out']=ConcurrencyProcessOutput::append($s['out'],(string)stream_get_contents($s['pipes'][1]));
     $s['err']=ConcurrencyProcessOutput::append($s['err'],(string)stream_get_contents($s['pipes'][2]));
     if($s['exit']!==null){$done++;continue;}
     $status=proc_get_status($s['proc']);if(!is_array($status))throw new \RuntimeException('Cannot inspect worker');
     if(!$status['running']){$s['exit']=(int)$status['exitcode'];ConcurrencyWorkerExit::assert($s['exit'],$s['err']);$done++;}
    }unset($s);
    if($done===2)break;
    if(ConcurrencyDeadline::expired($start,$timeoutMs,microtime(true)))throw new \RuntimeException('Concurrent worker deadline exceeded');
    usleep(20000);
   }
   foreach($state as &$s){$s['out']=ConcurrencyProcessOutput::append($s['out'],(string)stream_get_contents($s['pipes'][1]));$s['err']=ConcurrencyProcessOutput::append($s['err'],(string)stream_get_contents($s['pipes'][2]));ConcurrencyWorkerExit::assert($s['exit'],$s['err']);}unset($s);
  }finally {
   foreach($state as $s){
    if(is_resource($s['proc'])){$status=@proc_get_status($s['proc']);if(is_array($status)&&$status['running'])@proc_terminate($s['proc']);}
    foreach($s['pipes'] as $pipe)if(is_resource($pipe))fclose($pipe);
    if(is_resource($s['proc']))@proc_close($s['proc']);
   }
  }
 }
}
