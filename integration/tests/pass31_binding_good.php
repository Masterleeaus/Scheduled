<?php
require_once __DIR__.'/../../core/app/Booking/Public/PublicHost.php';
foreach(glob(__DIR__.'/../../core/app/Booking/Public/*.php') as $file)require_once $file;
use App\Booking\Public\{PublicHost,PublicHostnameGuard,PublicDomainStatus,PublicRatePolicy,PublicBookingFlags,PublicCatalogueSanitizer,PublicCatalogProjection,PublicDomainBinding};
function tryFail(callable $f):bool {try{$f();return false;}catch(\Throwable $e){return true;}}
if(!(PublicDomainBinding::validate((object)['domain'=>'a.example.com','status'=>'active','verified_at'=>'2026-11-09','owner_user_id'=>2])['owner_user_id']===2)){fwrite(STDERR,'binding_good failed\n');exit(1);} echo 'PASS binding_good\n';
