<?php
/**
 * Stripe Configuration Test
 * Run this to verify your Stripe API keys are properly configured
 */

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Stripe Configuration Test ===\n\n";

// Check if Stripe keys are set
$stripe_key = env('STRIPE_KEY');
$stripe_secret = env('STRIPE_SECRET');

echo "STRIPE_KEY: " . (empty($stripe_key) ? "NOT SET ❌" : "SET (last 10 chars: " . substr($stripe_key, -10) . ") ✓") . "\n";
echo "STRIPE_SECRET: " . (empty($stripe_secret) ? "NOT SET ❌" : "SET (last 10 chars: " . substr($stripe_secret, -10) . ") ✓") . "\n";
echo "STRIPE_WEBHOOK_SECRET: " . (empty(env('STRIPE_WEBHOOK_SECRET')) ? "NOT SET (optional)" : "SET ✓") . "\n\n";

if (empty($stripe_key) || empty($stripe_secret)) {
    echo "❌ Stripe API keys are not properly configured!\n";
    echo "Please add STRIPE_KEY and STRIPE_SECRET to your .env file.\n";
    exit(1);
}

// Try to use the Stripe API
try {
    \Stripe\Stripe::setApiKey($stripe_secret);
    
    // Try to retrieve account details to verify the key works
    $account = \Stripe\Account::retrieve();
    
    echo "✓ Stripe API connection successful!\n";
    echo "Account ID: " . $account->id . "\n";
    echo "Account Status: " . ($account->charges_enabled ? "Charges Enabled ✓" : "Charges Disabled ❌") . "\n";
    echo "\n=== Configuration is correct! ===\n";
    
} catch (\Stripe\Exception\InvalidRequestException $e) {
    echo "❌ Invalid Stripe API key: " . $e->getMessage() . "\n";
    exit(1);
} catch (\Exception $e) {
    echo "❌ Error connecting to Stripe: " . $e->getMessage() . "\n";
    exit(1);
}
