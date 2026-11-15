<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyResultStore::write($d,'b',ConcurrencyResult::rejected(new DomainException('Already booked')));check(ConcurrencyResultStore::read($d,'b')['outcome']==='rejected');}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 21_result_accepts_rejection\n';
