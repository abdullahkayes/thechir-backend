<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;

// Initialize Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test email
try {
    $testOrderId = "CHIR-2024-00001";
    
    // Create a test order if it doesn't exist
    if (!\App\Models\Order::where('order_id', $testOrderId)->exists()) {
        $order = new \App\Models\Order();
        $order->order_id = $testOrderId;
        $order->created_at = now();
        $order->sub_total = 100;
        $order->discount = 10;
        $order->total = 90;
        $order->user_id = 1;
        $order->save();
        
        // Create test billing record
        $billing = new \App\Models\Billing();
        $billing->order_id = $testOrderId;
        $billing->name = "Test Customer";
        $billing->email = "test@example.com";
        $billing->street = "123 Test Street";
        $billing->apartment = "Apt 1";
        $billing->city = "New York";
        $billing->state = "NY";
        $billing->zip = "10001";
        $billing->save();
        
        // Create test order product
        $orderProduct = new \App\Models\OrderProduct();
        $orderProduct->order_id = $testOrderId;
        $orderProduct->product_id = 1;
        $orderProduct->quantity = 1;
        $orderProduct->price = 100;
        $orderProduct->save();
    }
    
    // Try sending test email
    $email = "shahriar.manzoor.sadi@gmail.com";
    Mail::to($email)->send(new OrderMail($testOrderId));
    echo "✅ Email sent successfully to: " . $email . "\n";
    
    // Send to admin email
    $adminEmail = env('MAIL_USERNAME');
    if (!empty($adminEmail)) {
        Mail::to($adminEmail)->send(new OrderMail($testOrderId));
        echo "✅ Email sent successfully to admin: " . $adminEmail . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error sending email: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
