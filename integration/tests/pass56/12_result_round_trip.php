<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyResultStore::write($d,'a',ConcurrencyResult::accepted(123));check(ConcurrencyResultStore::read($d,'a')['booking_id']===123);}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 12_result_round_trip\n';
