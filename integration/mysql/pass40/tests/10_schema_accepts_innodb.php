<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/assert_schema.php';
Pass40Schema::validateEngines(array_fill_keys(Pass40Schema::TABLES,'InnoDB'));
echo 'PASS 10_schema_accepts_innodb'.PHP_EOL;
