<?php
require_once __DIR__.'/../../core/app/Booking/Public/PublicHost.php';
foreach(glob(__DIR__.'/../../core/app/Booking/Public/*.php') as $file)require_once $file;
use App\Booking\Public\{PublicHost,PublicHostnameGuard,PublicDomainStatus,PublicRatePolicy,PublicBookingFlags,PublicCatalogueSanitizer,PublicCatalogProjection,PublicDomainBinding};
function tryFail(callable $f):bool {try{$f();return false;}catch(\Throwable $e){return true;}}
if(!(PublicCatalogProjection::fromOwnerCatalogue(['services'=>[],'methods'=>[],'units'=>[],'staff'=>[],'preview_only'=>true,'booking_writes_enabled'=>false])['booking_writes_enabled']===false)){fwrite(STDERR,'catalog_shape failed\n');exit(1);} echo 'PASS catalog_shape\n';
