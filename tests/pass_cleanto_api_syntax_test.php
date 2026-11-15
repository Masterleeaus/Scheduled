<?php
// Regression guard for issue #55: every PHP file under private/cleanto/api must parse.
$dir = __DIR__.'/../private/cleanto/api';
$files = glob($dir.'/*.php') ?: [];
if (count($files) === 0) {
    fwrite(STDERR, "FAIL: no cleanto api files found\n");
    exit(1);
}
$failed = [];
foreach ($files as $file) {
    exec(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($file).' 2>&1', $out, $code);
    if ($code !== 0) {
        $failed[] = basename($file).': '.trim(implode(' ', $out));
    }
    $out = [];
}
if ($failed) {
    fwrite(STDERR, "FAIL: ".implode("\n", $failed)."\n");
    exit(1);
}
echo "PASS: ".count($files)." cleanto api files parse\n";
