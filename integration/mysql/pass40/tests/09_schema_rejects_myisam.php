<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/assert_schema.php';
$e=array_fill_keys(Pass40Schema::TABLES,'InnoDB');$e['ct_bookings']='MyISAM';try{Pass40Schema::validateEngines($e);throw new Exception('must fail');}catch(DomainException $x){}
echo 'PASS 09_schema_rejects_myisam'.PHP_EOL;
