<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void { $prefix='App\\Booking\\Public\\Redemption\\'; if(str_starts_with($class,$prefix))require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($class,strlen($prefix)).'.php'; });
$e=array_fill_keys(['public_writes_enabled','mysql_lifecycle_passed','concurrency_passed','pricing_parity_passed','tenant_isolation_passed','schema_passed','same_database_transaction','independent_persisted_readback','staff_locks_held_until_commit'],true);App\Booking\Public\Redemption\CommitReleaseEvidencePolicy::validate($e);
echo "PASS\n";
