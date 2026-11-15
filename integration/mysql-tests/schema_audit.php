<?php
declare(strict_types=1);
require __DIR__.'/connection.php';
try{$db=pass16_test_db();$result=(new Titan\BookingSidecar\BookingDatabaseSchemaAudit($db))->inspect();echo json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;exit($result['ready']?0:1);}catch(Throwable $e){fwrite(STDERR,$e->getMessage().PHP_EOL);exit(77);}
