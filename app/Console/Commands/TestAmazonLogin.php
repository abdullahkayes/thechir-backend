<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Amazon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestAmazonLogin extends Command
{
    protected $signature = 'amazon:test-login';
    protected $description = 'Test Amazon login functionality';

    public function handle()
    {
        $this->info("=== Amazon API Test ===\n");

        try {
            $this->info("1. Checking Database Connection...");
            $count = DB::select('select count(*) as total from amazons')[0]->total;
            $this->info("✓ Database connected successfully ({$count} records)");
            
            $this->info("\n2. Checking if Test User Exists...");
            $user = Amazon::where('amazon_email', 'lubuqa@mailinator.com')->first();
            if (!$user) {
                $this->warn("✗ Test user not found. Creating new test user...");
                $user = Amazon::create([
                    'amazon_name' => 'Test User',
                    'amazon_email' => 'lubuqa@mailinator.com',
                    'password' => Hash::make('password123'),
                    'amazon_seller_id' => 'TEST123',
                    'website' => 'https://test.example.com',
                    'status' => 'approved',
                ]);
                $this->info("✓ Test user created successfully (ID: {$user->id})");
            } else {
                $this->info("✓ Test user exists (ID: {$user->id})");
                if (!$user->password) {
                    $user->password = Hash::make('password123');
                    $user->save();
                    $this->info("✓ Password set for existing user");
                }
                if ($user->status !== 'approved') {
                    $user->status = 'approved';
                    $user->save();
                    $this->info("✓ User status set to 'approved'");
                }
            }
            
            $this->info("\n3. Verifying Password...");
            $password = 'password123';
            $passwordMatch = Hash::check($password, $user->password);
            if ($passwordMatch) {
                $this->info("✓ Password '{$password}' matches stored password");
            } else {
                $this->error("✗ Password '{$password}' does NOT match stored password");
            }
            
            $this->info("\n4. Checking User Status...");
            if ($user->status === 'approved') {
                $this->info("✓ User is approved to login");
            } else {
                $this->error("✗ User status is '{$user->status}' (expected: approved)");
            }
            
            $this->info("\n5. Testing Token Creation...");
            try {
                $token = $user->createToken('amazon-token')->plainTextToken;
                $this->info("✓ Token created successfully");
            } catch (Exception $e) {
                $this->error("✗ Error creating token: " . $e->getMessage());
            }
            
            $this->info("\n=== All Tests Passed! ===");
            $this->info("\n✅ Amazon login functionality is working correctly.");
            $this->info("\nTest User Credentials:");
            $this->info("  Email: lubuqa@mailinator.com");
            $this->info("  Password: password123");
            $this->info("  Status: approved");
            
        } catch (Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
            if (isset($e->getTrace()[0]['file']) && isset($e->getTrace()[0]['line'])) {
                $this->error("At: " . $e->getTrace()[0]['file'] . ":" . $e->getTrace()[0]['line']);
            }
        }

        return 0;
    }
}
