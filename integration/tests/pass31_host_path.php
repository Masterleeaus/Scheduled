<?php
require_once __DIR__.'/../../core/app/Booking/Public/PublicHost.php';
foreach(glob(__DIR__.'/../../core/app/Booking/Public/*.php') as $file)require_once $file;
use App\Booking\Public\{PublicHost,PublicHostnameGuard,PublicDomainStatus,PublicRatePolicy,PublicBookingFlags,PublicCatalogueSanitizer,PublicCatalogProjection,PublicDomainBinding};
function tryFail(callable $f):bool {try{$f();return false;}catch(\Throwable $e){return true;}}
if(!(tryFail(fn()=>PublicHost::normalize('cleaning.example.com/evil')))){fwrite(STDERR,'host_path failed\n');exit(1);} echo 'PASS host_path\n';
