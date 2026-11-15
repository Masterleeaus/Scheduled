<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$db=(new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();$l=new App\Booking\Public\Redemption\CommitLegacyLockBoundary(function(){throw new RuntimeException('acquire');},function(){throw new RuntimeException('cleanup');});try{$l->before($db,[]);throw new Exception('accepted');}catch(RuntimeException $e){if($e->getPrevious()?->getMessage()!=='acquire')throw new Exception('lost primary');}if(!$l->isHeldOrUncertain())throw new Exception('lost lease');
echo "PASS\n";
