<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check(ConcurrencyFixtureIdentity::from(validFixture())['staff_ids'] === [1]);
echo "PASS59 28_fixture_valid\n";
