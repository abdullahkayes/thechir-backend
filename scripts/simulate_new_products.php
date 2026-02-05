<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$out = [];
foreach (Product::with(['rel_to_gal','inventories.size','inventories.color'])->latest()->take(4)->get() as $p) {
    $inventories = $p->inventories;
    $colors = $inventories->pluck('color')->unique()->filter()->values()->map(function($c){ return $c ? ['id'=>$c->id ?? null,'name'=>$c->name ?? null] : null; })->filter()->values();
    $sizes = $inventories->pluck('size')->unique()->filter()->values()->map(function($s){ return $s ? ['id'=>$s->id ?? null,'name'=>$s->size ?? null] : null; })->filter()->values();
    $out[] = [
        'id' => $p->id,
        'name' => $p->product_name,
        'colors' => $colors,
        'sizes' => $sizes,
        'inventories_count' => $inventories->count(),
    ];
}

echo json_encode($out, JSON_PRETTY_PRINT);
