<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$n=0;$db=(new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();$l=new App\Booking\Public\Redemption\CommitLegacyLockBoundary(function()use(&$n){$n++;throw new RuntimeException('acquire failed');},function()use(&$n){$n++;});try{$l->before($db,[]);throw new Exception('accepted');}catch(RuntimeException $e){if($e->getMessage()!=='acquire failed')throw $e;}if($n!==2||$l->isHeldOrUncertain())throw new Exception('partial cleanup');
echo "PASS\n";
