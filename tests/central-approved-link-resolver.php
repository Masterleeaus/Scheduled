<?php
declare(strict_types=1);

/** Standalone CI check of the production account-link resolver (no database needed). */
require_once __DIR__.'/../core/app/Central/Link/CentralUserLinkResolver.php';
require_once __DIR__.'/../core/app/Central/Link/DatabaseCentralUserLinkResolver.php';

use App\Central\Link\DatabaseCentralUserLinkResolver;

$checks = 0;
$check = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        fwrite(STDERR, "FAIL: ".$message.PHP_EOL);
        exit(1);
    }
    ++$checks;
};
$lookups = [];
$resolver = new DatabaseCentralUserLinkResolver(
    static function (string $issuerDigest, string $subjectDigest, string $org) use (&$lookups): string {
        $lookups[] = [$issuerDigest, $subjectDigest, $org];
        return '42';
    }
);
$check($resolver->localUserId('https://central.example.test', 'subject-1', '0f8fad5b-d9cb-469f-a165-70867728950e') === 42, 'approved local user mapped');
$check($lookups[0] === [hash('sha256', 'https://central.example.test'), hash('sha256', 'subject-1'), '0f8fad5b-d9cb-469f-a165-70867728950e'], 'verified claims alone form the key');
$check($resolver->localUserId('', 'subject-1', '0f8fad5b-d9cb-469f-a165-70867728950e') === null, 'empty issuer denied');
$check($resolver->localUserId('https://central.example.test', '', '0f8fad5b-d9cb-469f-a165-70867728950e') === null, 'empty subject denied');
$check(count($lookups) === 1, 'invalid claims never reach storage');
$denied = new DatabaseCentralUserLinkResolver(static fn () => null);
$check($denied->localUserId('issuer', 'subject', '0f8fad5b-d9cb-469f-a165-70867728950e') === null, 'missing or revoked link denied');
foreach ([0, -1, '1.2', 'x@y.test', [], false] as $invalid) {
    $resolver = new DatabaseCentralUserLinkResolver(static fn () => $invalid);
    $check($resolver->localUserId('issuer', 'subject', '0f8fad5b-d9cb-469f-a165-70867728950e') === null, 'nonpositive or malformed local id denied');
}
echo "{$checks}/{$checks} Central approved account-link checks passed".PHP_EOL;
