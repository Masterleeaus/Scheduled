<?php
require_once __DIR__.'/../../core/app/Booking/Public/PublicHost.php';
foreach(glob(__DIR__.'/../../core/app/Booking/Public/*.php') as $file)require_once $file;
use App\Booking\Public\{PublicHost,PublicHostnameGuard,PublicDomainStatus,PublicRatePolicy,PublicBookingFlags,PublicCatalogueSanitizer,PublicCatalogProjection,PublicDomainBinding};
function tryFail(callable $f):bool {try{$f();return false;}catch(\Throwable $e){return true;}}
if(!(!str_contains(file_get_contents(__DIR__.'/../../core/routes/web.php'),"Route::post('/titan-booking/catalogue'"))){fwrite(STDERR,'route_read_only failed\n');exit(1);} echo 'PASS route_read_only\n';
