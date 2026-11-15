<?php
declare(strict_types=1);require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P65PDO();$slow=new P65Statement();$slow->delayUs=120000;$p->queue=[$slow,new P65Statement()];
$m=new BookingLockManager($p);$m->acquire(['tb:staff:2','tb:staff:1'],2);
p65(count($p->statements)===2,'expected both locks');
p65($p->statements[1]->params[1]<$p->statements[0]->params[1],'timeout was restarted for second lock');
p65($m->held()===['tb:staff:1','tb:staff:2'],'unordered staff keys');$m->releaseAll();
echo "pass65 01 shared budget OK\n";
