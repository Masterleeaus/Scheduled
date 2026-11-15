<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$db=(new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();$attempt=0;$l=new App\Booking\Public\Redemption\CommitLegacyLockBoundary(fn()=>null,function()use(&$attempt){if(++$attempt===1)throw new RuntimeException('release failure');});$l->before($db,[]);try{$l->after($db);throw new Exception('accepted');}catch(RuntimeException $e){}if(!$l->isHeldOrUncertain())throw new Exception('lost lease');$l->after($db);if($l->isHeldOrUncertain())throw new Exception('lease not released');
echo "PASS\n";
