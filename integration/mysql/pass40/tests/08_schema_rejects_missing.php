<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/assert_schema.php';
if(Pass40Schema::missing(['ct_bookings'])===[])throw new Exception('missing not detected');
echo 'PASS 08_schema_rejects_missing'.PHP_EOL;
