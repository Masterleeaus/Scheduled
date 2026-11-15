<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/acceptance_inventory.php';
$e=array_fill_keys(Pass40Inventory::required(),true);unset($e['actual_booking_commit']);if(Pass40Inventory::evaluate($e)['ready'])throw new Exception('skipped live booking');
echo 'PASS 15_inventory_requires_all'.PHP_EOL;
