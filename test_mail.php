<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Log;

// Set up logging
Log::info('Starting mail test');

$testOrderId = 'TEST-'.time();

try {
    Log::info('Attempting to send test email');
    
    $customerEmail = 'test@example.com';
    $adminEmail = env('MAIL_USERNAME');
    
    Log::info('Customer email: ' . $customerEmail);
    Log::info('Admin email: ' . $adminEmail);
    
    // Test sending to customer
    Mail::to($customerEmail)->send(new OrderMail($testOrderId));
    Log::info('Email sent to customer successfully');
    
    // Test sending to admin
    if (!empty($adminEmail)) {
        Mail::to($adminEmail)->send(new OrderMail($testOrderId));
        Log::info('Email sent to admin successfully');
    }
    
    echo "✅ Mail test completed successfully!\n";
    echo "Email sent to: $customerEmail\n";
    if (!empty($adminEmail)) {
        echo "Email sent to admin: $adminEmail\n";
    }
    
} catch (Exception $e) {
    Log::error('Mail test failed: ' . $e->getMessage());
    Log::error('Exception trace: ' . $e->getTraceAsString());
    
    echo "❌ Mail test failed:\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "\nFull exception:\n";
    print_r($e);
}
