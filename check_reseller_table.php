<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Resellers Table Structure ===\n\n";

// Get column information
$columns = DB::select("DESCRIBE resellers");

echo "Columns in resellers table:\n";
foreach ($columns as $column) {
    echo sprintf("  %-25s %-20s %s\n", $column->Field, $column->Type, $column->Null === 'NO' ? 'NOT NULL' : 'NULL');
}

echo "\n";
