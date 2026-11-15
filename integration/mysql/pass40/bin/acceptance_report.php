<?php
declare(strict_types=1);
require_once __DIR__.'/acceptance_inventory.php';
final class Pass40Report {
 public static function build(array $evidence):array {$result=Pass40Inventory::evaluate($evidence);return ['release_ready'=>$result['ready'],'missing'=>$result['missing'],'time_utc'=>gmdate('c'),'evidence'=>'operator-supplied; independent audit required'];}
 public static function save(string $file,array $report):void {if(!str_starts_with(realpath(dirname($file))?:'', '/tmp/'))throw new DomainException('Report must be under /tmp');$data=json_encode($report,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR);$temp=tempnam(dirname($file),'report_');if($temp===false)throw new RuntimeException('Temp failed');try{file_put_contents($temp,$data,LOCK_EX);chmod($temp,0600);if(!rename($temp,$file))throw new RuntimeException('Atomic publish failed');}finally{if(is_file($temp))unlink($temp);}}
}
