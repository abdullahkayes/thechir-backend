<?php
// Quick fix: Create commission for existing B2B order
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\B2b;
use App\Models\Order;
use App\Models\Commission;
use App\Models\Reseller;

echo "=== Setting Up Commission ===\n\n";

// Get reseller
$reseller = Reseller::first();
if (!$reseller) {
    echo "ERROR: No reseller found. Please create a reseller account first.\n";
    exit;
}
echo "Reseller: {$reseller->name} (ID: {$reseller->id})\n";
echo "unique_ref_id: {$reseller->unique_ref_id}\n\n";

// Get B2B user
$b2b = B2b::find(5);
if (!$b2b) {
    echo "ERROR: B2B user not found\n";
    exit;
}
echo "B2B User: {$b2b->business_name} (ID: {$b2b->id})\n";
echo "Current ref_id: " . ($b2b->ref_id ?: 'NULL') . "\n";

// Set ref_id if not set
if (!$b2b->ref_id) {
    $b2b->ref_id = $reseller->unique_ref_id;
    $b2b->save();
    echo "Updated ref_id to: {$b2b->ref_id}\n\n";
}

// Get the recent B2B order
$order = Order::where('b2b_id', $b2b->id)->orderBy('created_at', 'desc')->first();
if (!$order) {
    echo "ERROR: No order found for this B2B user\n";
    exit;
}
echo "Order: {$order->order_id} (ID: {$order->id})\n";
echo "Total: \${$order->total}\n\n";

// Check if commission already exists
$existing = Commission::where('order_id', $order->id)->first();
if ($existing) {
    echo "Commission already exists: ID={$existing->id}, Amount=\${$existing->amount}, Status={$existing->status}\n";
} else {
    // Create commission
    $commissionAmount = $order->total * 0.05;
    $commission = Commission::create([
        'reseller_id' => $reseller->id,
        'order_id' => $order->id,
        'amount' => $commissionAmount,
        'status' => 'pending',
    ]);
    echo "✓ Created commission: ID={$commission->id}, Amount=\${$commission->amount}, Status={$commission->status}\n";
    echo "  5% of \${$order->total} = \${$commissionAmount}\n";
}

echo "\n=== Done! Check reseller dashboard to see the commission. ===\n";
