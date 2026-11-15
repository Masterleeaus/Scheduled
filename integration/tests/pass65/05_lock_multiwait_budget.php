<?php
declare(strict_types=1);require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P65PDO();$a=new P65Statement();$a->delayUs=80000;$b=new P65Statement();$b->delayUs=80000;
$p->queue=[$a,$b,new P65Statement()];$m=new BookingLockManager($p);
$m->acquire(['tb:staff:3','tb:staff:2','tb:staff:1'],2);
$t=array_map(fn($s)=>(float)$s->params[1],array_slice($p->statements,0,3));
p65($t[0]>$t[1]&&$t[1]>$t[2],'cumulative deadline not enforced');$m->releaseAll();
echo "pass65 05 multiwait budget OK\n";
