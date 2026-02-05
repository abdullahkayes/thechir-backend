<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check slider_videos table structure
$columns = DB::select('SHOW COLUMNS FROM slider_videos');

echo "Slider Videos Table Structure:\n";
echo "=============================\n";

foreach ($columns as $column) {
    echo "Column Name: {$column->Field}\n";
    echo "Type: {$column->Type}\n";
    echo "Null: {$column->Null}\n";
    echo "Key: {$column->Key}\n";
    echo "Default: {$column->Default}\n";
    echo "Extra: {$column->Extra}\n";
    echo "-----------------------------\n";
}
