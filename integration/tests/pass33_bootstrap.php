<?php
declare(strict_types=1);
error_reporting(E_ALL);ini_set('assert.exception','1');
foreach(glob(__DIR__.'/../../core/app/Booking/Public/Intent/*.php') as $p)require_once $p;
require_once __DIR__.'/../../core/app/Booking/Public/PublicHost.php';
require_once __DIR__.'/../../core/app/Booking/Public/PublicTimezone.php';
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
function fixture():array{return ['service_id'=>1,'method_id'=>2,'unit_id'=>3,'staff_id'=>4,'quantity'=>2,'date_time'=>'2026-11-12T10:00:00','duration_minutes'=>60,'timezone'=>'Australia/Melbourne'];}
function expectException(callable $fn):void{$ok=false;try{$fn();}catch(\Throwable $e){$ok=true;}if(!$ok)throw new \RuntimeException('Expected rejection');}
