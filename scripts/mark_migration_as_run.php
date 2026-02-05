<?php
// Usage: php scripts/mark_migration_as_run.php migration_name
// Safely inserts a row into the migrations table so Laravel won't attempt
// to run a migration that would duplicate existing schema. DOES NOT
// modify or delete any existing data.

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migration = $argv[1] ?? null;
if (! $migration) {
    fwrite(STDERR, "Usage: php scripts/mark_migration_as_run.php migration_name\n");
    exit(1);
}

$db = $app->make('db');
$max = $db->table('migrations')->max('batch') ?: 0;
$batch = $max + 1;

// Check if migration already recorded
$exists = $db->table('migrations')->where('migration', $migration)->exists();
if ($exists) {
    echo "Migration '$migration' already present in migrations table.\n";
    exit(0);
}

$db->table('migrations')->insert([
    'migration' => $migration,
    'batch' => $batch,
]);

echo "Inserted migration '$migration' with batch $batch into migrations table.\n";
