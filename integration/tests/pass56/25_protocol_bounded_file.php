<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{$p=$d.'/result-a.json';file_put_contents($p,'{}');check(ConcurrencyProtocol::boundedFile($p)==='{}');}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 25_protocol_bounded_file\n';
