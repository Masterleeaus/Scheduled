<?php
require __DIR__.'/../core/app/Support/Install/LegacySqlImporter.php';
use App\Support\Install\LegacySqlImporter;
$schema = file_get_contents(__DIR__.'/../installer/database.sql');
$count = 0; $excluded = [];
foreach (LegacySqlImporter::statements($schema) as $statement) {
    if (LegacySqlImporter::isDemoAccountInsert($statement)) {
        if (preg_match('/INTO\s+`?(admins|users)`?/i', $statement, $m)) $excluded[] = strtolower($m[1]);
    } else { $count++; }
}
if (!in_array('admins', $excluded, true) || !in_array('users', $excluded, true)) {
    fwrite(STDERR, "Demo account SQL exclusion failed\n"); exit(1);
}
foreach (['INSERT INTO `admins` VALUES (1)', 'insert into users values (1)', 'REPLACE INTO `users` VALUES (2)'] as $sample) {
    if (!LegacySqlImporter::isDemoAccountInsert($sample)) exit(2);
}
foreach (['INSERT INTO `services` VALUES (1)', 'UPDATE admins SET password=0', 'SELECT * FROM users'] as $sample) {
    if (LegacySqlImporter::isDemoAccountInsert($sample)) exit(3);
}
echo "PASS: excluded accounts: ".implode(', ', $excluded)."; retained SQL statements: $count\n";
