<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/acceptance_inventory.php';
if(Pass40Inventory::evaluate([])['ready'])throw new Exception('premature release');
echo 'PASS 14_inventory_fails_closed'.PHP_EOL;
