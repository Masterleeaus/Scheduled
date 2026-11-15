<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {$f=validFixture();$f['create']['staff_ids']=['2147483648'];ConcurrencyFixtureIdentity::from($f);});
echo "PASS59 27_fixture_bad_staff\n";
