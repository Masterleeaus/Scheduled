<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {$f=validFixture();$f['create']['start_at']='2027-02-30 09:00:00';ConcurrencyFixtureIdentity::from($f);});
echo "PASS59 26_fixture_bad_date\n";
