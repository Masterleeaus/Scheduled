<?php
require_once __DIR__.'/../../core/app/Booking/Public/PublicHost.php';
foreach(glob(__DIR__.'/../../core/app/Booking/Public/*.php') as $file)require_once $file;
use App\Booking\Public\{PublicHost,PublicHostnameGuard,PublicDomainStatus,PublicRatePolicy,PublicBookingFlags,PublicCatalogueSanitizer,PublicCatalogProjection,PublicDomainBinding};
function tryFail(callable $f):bool {try{$f();return false;}catch(\Throwable $e){return true;}}
if(!(!PublicDomainStatus::publicEnabled('active',null))){fwrite(STDERR,'status_unverified failed\n');exit(1);} echo 'PASS status_unverified\n';
