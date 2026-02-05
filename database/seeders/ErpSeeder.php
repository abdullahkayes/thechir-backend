<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountType;
use App\Models\Account;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockDetail;
use App\Models\InventoryMovement;
use App\Models\AccountingEntry;
use App\Models\AccountingEntryLine;
use Carbon\Carbon;

class ErpSeeder extends Seeder
{
    public function run()
    {
        // Create Account Types
        $assetType = AccountType::firstOrCreate([
            'name' => 'Asset',
            'code' => 'ASSET',
            'normal_balance' => 'debit'
        ]);
        $liabilityType = AccountType::firstOrCreate([
            'name' => 'Liability',
            'code' => 'LIAB',
            'normal_balance' => 'credit'
        ]);
        $equityType = AccountType::firstOrCreate([
            'name' => 'Equity',
            'code' => 'EQUITY',
            'normal_balance' => 'credit'
        ]);
        $revenueType = AccountType::firstOrCreate([
            'name' => 'Revenue',
            'code' => 'REV',
            'normal_balance' => 'credit'
        ]);
        $expenseType = AccountType::firstOrCreate([
            'name' => 'Expense',
            'code' => 'EXP',
            'normal_balance' => 'debit'
        ]);

        // Create Accounts
        $inventoryAccount = Account::firstOrCreate([
            'name' => 'Inventory',
            'account_type_id' => $assetType->id,
            'code' => 'INV-001'
        ], ['balance' => 0]);

        $salesAccount = Account::firstOrCreate([
            'name' => 'Sales Revenue',
            'account_type_id' => $revenueType->id,
            'code' => 'REV-001'
        ], ['balance' => 0]);

        $cogsAccount = Account::firstOrCreate([
            'name' => 'Cost of Goods Sold',
            'account_type_id' => $expenseType->id,
            'code' => 'COGS-001'
        ], ['balance' => 0]);

        // Create Suppliers
        $supplier1 = Supplier::firstOrCreate([
            'name' => 'Cosmetic Distributors Ltd.',
            'email' => 'contact@cosmeticdist.com',
            'phone' => '+1-555-0123',
            'address' => '123 Beauty Street, NY 10001'
        ]);

        $supplier2 = Supplier::firstOrCreate([
            'name' => 'Natural Beauty Supplies',
            'email' => 'info@naturalbeauty.com',
            'phone' => '+1-555-0456',
            'address' => '456 Organic Ave, CA 90210'
        ]);

        // Create sample products if they don't exist
        $products = Product::all();
        if ($products->isEmpty()) {
            $product1 = Product::create([
                'product_name' => 'Luxury Face Cream',
                'sku' => 'LFC-001',
                'price' => 45.99,
                'short_desp' => 'Premium anti-aging face cream',
                'long_desp' => 'Luxury face cream with natural ingredients for anti-aging benefits.',
                'category_id' => 1,
                'subcategory_id' => 1,
                'preview' => 'upload/product/preview/face-cream.jpg',
                'status' => 1
            ]);

            $product2 = Product::create([
                'product_name' => 'Natural Lip Balm',
                'sku' => 'NLB-002',
                'price' => 12.99,
                'short_desp' => 'Moisturizing lip balm',
                'long_desp' => 'Natural lip balm with beeswax and essential oils.',
                'category_id' => 1,
                'subcategory_id' => 2,
                'preview' => 'upload/product/preview/lip-balm.jpg',
                'status' => 1
            ]);

            $product3 = Product::create([
                'product_name' => 'Organic Shampoo',
                'sku' => 'OSH-003',
                'price' => 28.50,
                'short_desp' => 'Gentle organic shampoo',
                'long_desp' => 'Organic shampoo for all hair types.',
                'category_id' => 2,
                'subcategory_id' => 3,
                'preview' => 'upload/product/preview/shampoo.jpg',
                'status' => 1
            ]);

            $products = collect([$product1, $product2, $product3]);
        }

        // Create Purchase Orders and Stock Details
        foreach ($products as $product) {
            $supplier = rand(0, 1) ? $supplier1 : $supplier2;
            $quantity = rand(50, 200);
            $unitPrice = $product->price * 0.6; // 60% of selling price as cost

            // Create Purchase Order
            $po = PurchaseOrder::create([
                'po_number' => 'PO-' . strtoupper(substr(md5(rand()), 0, 8)),
                'supplier_id' => $supplier->id,
                'order_date' => Carbon::now()->subDays(rand(1, 30)),
                'expected_delivery_date' => Carbon::now()->addDays(rand(1, 7)),
                'received_date' => Carbon::now()->subDays(rand(1, 7)),
                'status' => 'received',
                'subtotal' => $quantity * $unitPrice,
                'total_amount' => $quantity * $unitPrice,
                'paid_amount' => $quantity * $unitPrice,
                'payment_status' => 'paid'
            ]);

            // Create Purchase Order Item
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost' => $unitPrice,
                'total_cost' => $quantity * $unitPrice,
                'received_quantity' => $quantity
            ]);

            // Create Stock Detail
            $stockDetail = StockDetail::create([
                'product_id' => $product->id,
                'purchase_order_id' => $po->id,
                'lot_number' => 'LOT-' . strtoupper(substr(md5(rand()), 0, 8)),
                'purchase_price' => $unitPrice,
                'quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'expiry_date' => Carbon::now()->addMonths(rand(6, 24)),
                'received_date' => Carbon::now()->subDays(rand(1, 7)),
                'status' => 'available'
            ]);

            // Create Inventory Movement
            InventoryMovement::create([
                'product_id' => $product->id,
                'stock_detail_id' => $stockDetail->id,
                'movement_type' => 'IN',
                'quantity' => $quantity,
                'unit_cost' => $unitPrice,
                'total_value' => $quantity * $unitPrice,
                'reference_type' => 'purchase_order',
                'reference_id' => $po->id,
                'reason' => 'Stock received from supplier'
            ]);

            // Create Accounting Entry for Inventory Purchase
            $entry = AccountingEntry::create([
                'entry_number' => 'PO-' . $po->id . '-' . time(),
                'entry_date' => Carbon::now(),
                'description' => "Inventory purchase - {$product->product_name}",
                'reference_type' => 'purchase_order',
                'reference_id' => $po->id,
                'total_amount' => $quantity * $unitPrice,
                'status' => 'posted'
            ]);

            // Debit Inventory Account
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_id' => $inventoryAccount->id,
                'type' => 'debit',
                'amount' => $quantity * $unitPrice
            ]);

            // Credit Accounts Payable (assuming we have it)
            $accountsPayable = Account::firstOrCreate([
                'name' => 'Accounts Payable',
                'account_type_id' => $liabilityType->id,
                'code' => 'AP-001'
            ], ['balance' => 0]);

            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_id' => $accountsPayable->id,
                'type' => 'credit',
                'amount' => $quantity * $unitPrice
            ]);

            // Update account balances
            $inventoryAccount->increment('balance', $quantity * $unitPrice);
            $accountsPayable->increment('balance', $quantity * $unitPrice);
        }

        $this->command->info('ERP data seeded successfully!');
    }
}
