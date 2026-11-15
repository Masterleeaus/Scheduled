<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {$o=p59_order();$o['order_id']='2147483648';LifecycleClientOrderRow::parse($o);});
echo "PASS59 25_order_overflow\n";
