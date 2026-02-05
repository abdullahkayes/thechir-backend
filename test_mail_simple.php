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
    $email = "shahriar.manzoor.sadi@gmail.com";
    
    // Create a mock order ID
    $testOrderId = "CHIR-2024-00001";
    
    echo "Attempting to send email to: " . $email . "\n";
    Mail::to($email)->send(new OrderMail($testOrderId));
    echo "✅ Email sent successfully to: " . $email . "\n";
    
    // Send to admin email
    $adminEmail = env('MAIL_USERNAME');
    if (!empty($adminEmail)) {
        echo "Attempting to send email to admin: " . $adminEmail . "\n";
        Mail::to($adminEmail)->send(new OrderMail($testOrderId));
        echo "✅ Email sent successfully to admin: " . $adminEmail . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error sending email: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
