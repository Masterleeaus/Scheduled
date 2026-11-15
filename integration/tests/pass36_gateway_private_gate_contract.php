<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$f=file_get_contents(__DIR__.'/../../core/app/Booking/Public/Redemption/CommitLegacyGateway.php');if(!str_contains($f,'CommitReleaseEvidencePolicy::validate')||!str_contains($f,'new RedeemLedger'))throw new Exception('release delegation contract');
$policy=file_get_contents(__DIR__.'/../../core/app/Booking/Public/Redemption/CommitReleaseEvidencePolicy.php');
if(!str_contains($policy,'CommitLegacyRelease::require')||!str_contains($policy,'same_database_transaction')||!str_contains($policy,'independent_persisted_readback')||!str_contains($policy,'staff_locks_held_until_commit'))throw new Exception('release enforcement contract');
echo "PASS\n";
