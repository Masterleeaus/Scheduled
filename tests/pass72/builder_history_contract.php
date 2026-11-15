<?php
$base = dirname(__DIR__, 2);
$controller = file_get_contents($base . '/core/app/Http/Controllers/Admin/PageBuilderController.php');
$routes = file_get_contents($base . '/core/routes/web.php');
$view = file_get_contents($base . '/core/resources/views/admin/pagebuilder/content.blade.php');
$migration = file_get_contents($base . '/core/database/migrations/2026_11_10_000072_create_builder_revisions.php');
$checks = [
    'save holds row lock' => strpos($controller, '->lockForUpdate()->first()') !== false,
    'save uses transaction' => strpos($controller, 'return DB::transaction(function () use ($request, $language, $type)') !== false,
    'revision required' => strpos($controller, "'revision'))) {") !== false,
    'conflict returns 409' => strpos($controller, "'revision' => " . '$revision' . '], 409') !== false,
    'snapshot before update' => strpos($controller, '$this->storeBuilderSnapshot($data, $type, $language->id, $revision);') < strpos($controller, '$data->html = $html;'),
    'unique revision key' => strpos($migration, 'builder_revision_unique') !== false,
    'atomic deduplication' => strpos($controller, "->insertOrIgnore([") !== false,
    'history scoped by language' => strpos($controller, "->where('language_id', " . '$language->id)') !== false,
    'history capped' => strpos($controller, "->limit(30)") !== false,
    'restore locked' => strpos($controller, '$this->builderTarget($request, true)') !== false,
    'restore checks current revision' => strpos($controller, "'Page changed; reload before restoring.'") !== false,
    'restore snapshots outgoing version' => strpos($controller, '$this->storeBuilderSnapshot($data, $request->input(\'type\'), $language->id, $current);') !== false,
    'history route registered' => strpos($routes, "name('admin.pagebuilder.history')") !== false,
    'restore route registered' => strpos($routes, "name('admin.pagebuilder.restore')") !== false,
    'history editor control' => strpos($view, "id: 'builder-history'") !== false,
    'save labeled live' => strpos($view, 'Save and publish live') !== false,
    'confirmation on restore' => strpos($view, "window.confirm('Restore this version") !== false,
    'stale restore response handled' => strpos($view, "xhr.status === 409") !== false,
    'migration reversible' => strpos($migration, "Schema::dropIfExists('builder_revisions')") !== false,
];
$fail = 0;
foreach ($checks as $label => $ok) {
    echo ($ok ? 'PASS' : 'FAIL') . ': ' . $label . "\n";
    if (!$ok) $fail++;
}
echo sprintf("TOTAL %d/%d passed\n", count($checks) - $fail, count($checks));
exit($fail ? 1 : 0);
