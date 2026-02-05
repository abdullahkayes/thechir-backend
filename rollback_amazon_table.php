<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Starting manual rollback of amazons table...\n";

try {
    // Check if the amazons table exists
    if (Schema::hasTable('amazons')) {
        echo "✓ amazons table exists\n";
        
        // Drop foreign key from carts table
        if (Schema::hasColumn('carts', 'amazon_id')) {
            echo "Dropping foreign key from carts table...\n";
            Schema::table('carts', function (Blueprint $table) {
                $table->dropForeign(['amazon_id']);
                $table->dropColumn(['amazon_id']);
            });
            echo "✓ Foreign key from carts table dropped\n";
        }
        
        // Drop foreign key from orders table
        if (Schema::hasColumn('orders', 'amazon_id')) {
            echo "Dropping foreign key from orders table...\n";
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['amazon_id']);
                $table->dropColumn('amazon_id');
            });
            echo "✓ Foreign key from orders table dropped\n";
        }
        
        // Drop foreign key from billings table
        if (Schema::hasColumn('billings', 'amazon_id')) {
            echo "Dropping foreign key from billings table...\n";
            Schema::table('billings', function (Blueprint $table) {
                $table->dropForeign(['amazon_id']);
                $table->dropColumn('amazon_id');
            });
            echo "✓ Foreign key from billings table dropped\n";
        }
        
        // Drop amazon_price column from product_inventories table
        if (Schema::hasColumn('product_inventories', 'amazon_price')) {
            echo "Dropping amazon_price column from product_inventories table...\n";
            Schema::table('product_inventories', function (Blueprint $table) {
                $table->dropColumn('amazon_price');
            });
            echo "✓ amazon_price column dropped\n";
        }
        
        // Drop additional columns from amazons table
        if (Schema::hasColumn('amazons', 'amazon_seller_id')) {
            echo "Dropping additional columns from amazons table...\n";
            Schema::table('amazons', function (Blueprint $table) {
                $table->dropColumn(['amazon_seller_id', 'website']);
            });
            echo "✓ Additional columns dropped\n";
        }
        
        // Drop the amazons table
        echo "Dropping amazons table...\n";
        Schema::dropIfExists('amazons');
        echo "✓ amazons table dropped\n";
        
        // Remove migration records from the migrations table
        $migrations = [
            '2026_01_15_000000_create_amazons_table',
            '2026_01_15_000001_add_amazon_id_to_carts_table',
            '2026_01_15_000002_add_amazon_id_to_orders_table',
            '2026_01_15_000003_add_amazon_id_to_billings_table',
            '2026_01_15_000004_add_amazon_price_to_product_inventories_table',
            '2026_02_03_000000_add_amazon_seller_id_and_website_to_amazons_table'
        ];
        
        foreach ($migrations as $migration) {
            DB::table('migrations')->where('migration', $migration)->delete();
            echo "✓ Migration record '$migration' removed\n";
        }
        
        echo "\n✅ Successfully rolled back the amazons table and all related dependencies!\n";
        
    } else {
        echo "ℹ️ amazons table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nChecking migration status...\n";
echo shell_exec('cd ' . __DIR__ . ' && php artisan migrate:status | findstr -i amazon');
