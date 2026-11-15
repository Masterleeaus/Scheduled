<?php
// Pass 71: source contract and save-guard regression tests (no application boot required).
$root = dirname(__DIR__, 2);
$c = file_get_contents($root . '/core/app/Http/Controllers/Admin/PageBuilderController.php');
$v = file_get_contents($root . '/core/resources/views/admin/pagebuilder/content.blade.php');
$tests = [
    'page language scoped on load' => strpos($c, "Page::where('id', \$request->id)->where('language_id', \$lang->id)") !== false,
    'page language scoped on save' => strpos($c, "where('language_id', \$language->id)") !== false,
    'unknown types refused' => strpos($c, 'required|in:page,themeHome') !== false,
    'builder payload size limits' => strpos($c, 'max:4194304') !== false,
    'JSON state checked' => strpos($c, 'json_last_error() !== JSON_ERROR_NONE') !== false,
    'optimistic concurrency' => strpos($c, 'hash_equals($revision') !== false,
    'conflict response 409' => strpos($c, '], 409)') !== false,
    'server returns revision' => strpos($c, "'ok' => true, 'revision' => \$revision") !== false,
    'client submits revision' => strpos($v, "fd.append('revision', builderRevision)") !== false,
    'client sends language' => strpos($v, "fd.append('language', @json(\$lang->code))") !== false,
    'client prevents concurrent saves' => strpos($v, 'if (builderSaveInFlight) return;') !== false,
    'client handles conflict' => strpos($v, 'xhr.status === 409') !== false,
    'client checks server acknowledgement' => strpos($v, 'result.ok !== true') !== false,
    'client resets spinner' => strpos($v, 'builderSaveInFlight = false;') !== false,
    'client has error handler' => strpos($v, 'error: function(xhr)') !== false,
];
foreach ($tests as $name => $passed) { echo ($passed ? 'PASS' : 'FAIL') . ' ' . $name . "\n"; }
exit(in_array(false, $tests, true) ? 1 : 0);
