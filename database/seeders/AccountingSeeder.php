<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create account types
        $assetType = AccountType::firstOrCreate(['name' => 'Asset']);
        $liabilityType = AccountType::firstOrCreate(['name' => 'Liability']);
        $equityType = AccountType::firstOrCreate(['name' => 'Equity']);
        $revenueType = AccountType::firstOrCreate(['name' => 'Revenue']);
        $expenseType = AccountType::firstOrCreate(['name' => 'Expense']);

        // Create essential accounts
        $accounts = [
            [
                'account_type_id' => $assetType->id,
                'name' => 'Inventory Asset',
                'code' => 'ASSET-INV',
                'description' => 'Inventory asset account',
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'account_type_id' => $assetType->id,
                'name' => 'Cash',
                'code' => 'ASSET-CASH',
                'description' => 'Cash account',
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'account_type_id' => $assetType->id,
                'name' => 'Accounts Receivable',
                'code' => 'ASSET-AR',
                'description' => 'Accounts receivable',
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'account_type_id' => $assetType->id,
                'name' => 'Bank',
                'code' => 'ASSET-BANK',
                'description' => 'Bank account',
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'account_type_id' => $liabilityType->id,
                'name' => 'Accounts Payable',
                'code' => 'LIAB-AP',
                'description' => 'Accounts payable',
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'account_type_id' => $equityType->id,
                'name' => 'Retained Earnings',
                'code' => 'EQUITY-RE',
                'description' => 'Retained earnings',
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'account_type_id' => $revenueType->id,
                'name' => 'Sales Revenue',
                'code' => 'REV-SALES',
                'description' => 'Sales revenue',
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'account_type_id' => $expenseType->id,
                'name' => 'Cost of Goods Sold',
                'code' => 'EXP-COGS',
                'description' => 'Cost of goods sold',
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'account_type_id' => $expenseType->id,
                'name' => 'Loss from Damage',
                'code' => 'EXP-LOSS',
                'description' => 'Loss from damaged goods',
                'balance' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $accountData) {
            Account::firstOrCreate(
                ['code' => $accountData['code']],
                $accountData
            );
        }
    }
}