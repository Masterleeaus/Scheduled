<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/acceptance_inventory.php';
$e=array_fill_keys(Pass40Inventory::required(),true);if(!Pass40Inventory::evaluate($e)['ready'])throw new Exception('evidence missing');
echo 'PASS 16_inventory_accepts_complete_evidence'.PHP_EOL;
