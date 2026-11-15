<?php
declare(strict_types=1);

/**
 * Regression gate for Cleanto booking endpoints (Scheduled #55).
 *
 * These are legacy first-party PHP endpoints retained until their logic is
 * integrated with Scheduled's single Laravel booking runtime. Lint, do not
 * include or execute them: they require legacy credentials and globals.
 * The standard GitHub CI "Other PHP test scripts" step runs this file.
 */
$root = dirname(__DIR__);
$paths = [
    'private/cleanto/api/appointment_cancel.php',
    'private/cleanto/api/appointment_complete.php',
    'private/cleanto/api/appointment_confirm.php',
    'private/cleanto/api/appointment_reject.php',
    'private/cleanto/api/appointment_reshedule.php',
    'private/cleanto/api/booking_assign_staff.php',
    'private/cleanto/api/index.php',
];

$failures = [];
$plivoCalls = 0;

foreach ($paths as $relative) {
    $file = $root . '/' . $relative;
    if (!is_file($file) || !is_readable($file)) {
        $failures[] = "$relative is missing or unreadable";
        continue;
    }

    $source = file_get_contents($file);
    if ($source === false) {
        $failures[] = "$relative could not be inspected";
        continue;
    }

    // A space after the PHP namespace separator breaks parsing.
    if (preg_match('/\bPlivo\\\\\s+RestAPI\b/', $source)) {
        $failures[] = "$relative contains malformed Plivo namespace syntax";
    }
    $plivoCalls += preg_match_all('/\bnew\s+\\\\?Plivo\\\\RestAPI\s*\(/', $source);

    $output = [];
    $code = 0;
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file) . ' 2>&1', $output, $code);
    if ($code !== 0) {
        $failures[] = "$relative failed php -l: " . implode(' ', $output);
    }
}

if (count($paths) !== 7 || $plivoCalls < 11) {
    $failures[] = "Expected the seven named booking API files and at least eleven Plivo call sites; found $plivoCalls";
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL: $failure\n");
    }
    exit(1);
}

echo "PASS: seven preserved Cleanto API files parse and $plivoCalls Plivo calls use a valid namespace\n";
