<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountingEntry;
use App\Models\AccountingEntryLine;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Create a journal entry
     */
    public function createEntry($data, $lines)
    {
        DB::beginTransaction();
        try {
            // Validate that debits equal credits
            $totalDebits = collect($lines)->where('type', 'debit')->sum('amount');
            $totalCredits = collect($lines)->where('type', 'credit')->sum('amount');

            if (abs($totalDebits - $totalCredits) > 0.01) {
                throw new \Exception('Debits must equal credits');
            }

            // Create entry
            $entry = AccountingEntry::create([
                'entry_date' => $data['entry_date'] ?? now(),
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'description' => $data['description'] ?? null,
                'total_amount' => $totalDebits,
                'status' => 'posted',
                'user_id' => auth()->id(),
            ]);

            // Create entry lines and update account balances
            foreach ($lines as $line) {
                AccountingEntryLine::create([
                    'accounting_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'type' => $line['type'],
                    'amount' => $line['amount'],
                    'description' => $line['description'] ?? null,
                ]);

                // Update account balance
                $account = Account::find($line['account_id']);
                if ($line['type'] === 'debit') {
                    $account->debit($line['amount']);
                } else {
                    $account->credit($line['amount']);
                }
            }

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Record purchase transaction
     */
    public function recordPurchase($purchaseOrder)
    {
        $inventoryAccount = Account::where('code', 'ASSET-INV')->first();
        $payableAccount = Account::where('code', 'LIAB-AP')->first();

        if (!$inventoryAccount || !$payableAccount) {
            throw new \Exception('Required accounts not found');
        }

        $lines = [
            [
                'account_id' => $inventoryAccount->id,
                'type' => 'debit',
                'amount' => $purchaseOrder->total_amount,
                'description' => 'Inventory purchase - PO: ' . $purchaseOrder->po_number,
            ],
            [
                'account_id' => $payableAccount->id,
                'type' => 'credit',
                'amount' => $purchaseOrder->total_amount,
                'description' => 'Accounts payable - PO: ' . $purchaseOrder->po_number,
            ],
        ];

        return $this->createEntry([
            'entry_date' => $purchaseOrder->received_date ?? now(),
            'reference_type' => 'App\Models\PurchaseOrder',
            'reference_id' => $purchaseOrder->id,
            'description' => 'Purchase from ' . $purchaseOrder->supplier->name,
        ], $lines);
    }

    /**
     * Record sale transaction
     */
    public function recordSale($order, $cogs)
    {
        $cashAccount = Account::where('code', 'ASSET-CASH')->first();
        $receivableAccount = Account::where('code', 'ASSET-AR')->first();
        $revenueAccount = Account::where('code', 'REV-SALES')->first();
        $cogsAccount = Account::where('code', 'EXP-COGS')->first();
        $inventoryAccount = Account::where('code', 'ASSET-INV')->first();

        if (!$cashAccount || !$revenueAccount || !$cogsAccount || !$inventoryAccount) {
            throw new \Exception('Required accounts not found');
        }

        // Revenue entry
        $revenueLines = [
            [
                'account_id' => $order->payment_method === 'cash' ? $cashAccount->id : $receivableAccount->id,
                'type' => 'debit',
                'amount' => $order->total,
                'description' => 'Sale - Order: ' . $order->order_id,
            ],
            [
                'account_id' => $revenueAccount->id,
                'type' => 'credit',
                'amount' => $order->total,
                'description' => 'Sales revenue - Order: ' . $order->order_id,
            ],
        ];

        $this->createEntry([
            'entry_date' => $order->created_at,
            'reference_type' => 'App\Models\Order',
            'reference_id' => $order->id,
            'description' => 'Sale to customer',
        ], $revenueLines);

        // COGS entry
        $cogsLines = [
            [
                'account_id' => $cogsAccount->id,
                'type' => 'debit',
                'amount' => $cogs,
                'description' => 'Cost of goods sold - Order: ' . $order->order_id,
            ],
            [
                'account_id' => $inventoryAccount->id,
                'type' => 'credit',
                'amount' => $cogs,
                'description' => 'Inventory reduction - Order: ' . $order->order_id,
            ],
        ];

        return $this->createEntry([
            'entry_date' => $order->created_at,
            'reference_type' => 'App\Models\Order',
            'reference_id' => $order->id,
            'description' => 'COGS for sale',
        ], $cogsLines);
    }

    /**
     * Record payment to supplier
     */
    public function recordSupplierPayment($supplier, $amount, $paymentMethod = 'cash')
    {
        $payableAccount = Account::where('code', 'LIAB-AP')->first();
        $cashAccount = Account::where('code', 'ASSET-CASH')->first();
        $bankAccount = Account::where('code', 'ASSET-BANK')->first();

        $paymentAccount = $paymentMethod === 'cash' ? $cashAccount : $bankAccount;

        $lines = [
            [
                'account_id' => $payableAccount->id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => 'Payment to supplier: ' . $supplier->name,
            ],
            [
                'account_id' => $paymentAccount->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => 'Payment via ' . $paymentMethod,
            ],
        ];

        return $this->createEntry([
            'entry_date' => now(),
            'description' => 'Supplier payment',
        ], $lines);
    }

    /**
     * Get profit and loss statement
     */
    public function getProfitLoss($startDate, $endDate)
    {
        $revenue = AccountingEntryLine::whereHas('account', function ($q) {
                $q->whereHas('accountType', function ($q2) {
                    $q2->where('name', 'Revenue');
                });
            })
            ->whereHas('accountingEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate])
                  ->where('status', 'posted');
            })
            ->where('type', 'credit')
            ->sum('amount');

        $cogs = AccountingEntryLine::whereHas('account', function ($q) {
                $q->where('code', 'EXP-COGS');
            })
            ->whereHas('accountingEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate])
                  ->where('status', 'posted');
            })
            ->where('type', 'debit')
            ->sum('amount');

        $expenses = AccountingEntryLine::whereHas('account', function ($q) {
                $q->whereHas('accountType', function ($q2) {
                    $q2->where('name', 'Expense');
                })->where('code', '!=', 'EXP-COGS');
            })
            ->whereHas('accountingEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate])
                  ->where('status', 'posted');
            })
            ->where('type', 'debit')
            ->sum('amount');

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses;

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $expenses,
            'net_profit' => $netProfit,
            'gross_margin' => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0,
            'net_margin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
        ];
    }

    /**
     * Record loss transaction
     */
    public function recordLoss($productId, $amount, $description)
    {
        $lossAccount = Account::where('code', 'EXP-LOSS')->first();
        $inventoryAccount = Account::where('code', 'ASSET-INV')->first();

        if (!$lossAccount || !$inventoryAccount) {
            throw new \Exception('Required accounts not found');
        }

        $lines = [
            [
                'account_id' => $lossAccount->id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => $description,
            ],
            [
                'account_id' => $inventoryAccount->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => $description,
            ],
        ];

        return $this->createEntry([
            'entry_date' => now(),
            'description' => $description,
        ], $lines);
    }

    /**
     * Reverse sale transaction
     */
    public function reverseSale($order, $cogsAmount)
    {
        $cashAccount = Account::where('code', 'ASSET-CASH')->first();
        $receivableAccount = Account::where('code', 'ASSET-AR')->first();
        $revenueAccount = Account::where('code', 'REV-SALES')->first();
        $cogsAccount = Account::where('code', 'EXP-COGS')->first();
        $inventoryAccount = Account::where('code', 'ASSET-INV')->first();

        if (!$cashAccount || !$revenueAccount || !$cogsAccount || !$inventoryAccount) {
            throw new \Exception('Required accounts not found');
        }

        // Reverse revenue entry
        $revenueLines = [
            [
                'account_id' => $order->payment_method === 'cash' ? $cashAccount->id : $receivableAccount->id,
                'type' => 'credit', // Reverse the debit
                'amount' => $order->total,
                'description' => 'Return - Order: ' . $order->order_id,
            ],
            [
                'account_id' => $revenueAccount->id,
                'type' => 'debit', // Reverse the credit
                'amount' => $order->total,
                'description' => 'Return - Sales revenue - Order: ' . $order->order_id,
            ],
        ];

        $this->createEntry([
            'entry_date' => now(),
            'reference_type' => 'App\Models\Order',
            'reference_id' => $order->id,
            'description' => 'Return - Sale to customer',
        ], $revenueLines);

        // Reverse COGS entry
        $cogsLines = [
            [
                'account_id' => $cogsAccount->id,
                'type' => 'credit', // Reverse the debit
                'amount' => $cogsAmount,
                'description' => 'Return - Cost of goods sold - Order: ' . $order->order_id,
            ],
            [
                'account_id' => $inventoryAccount->id,
                'type' => 'debit', // Reverse the credit
                'amount' => $cogsAmount,
                'description' => 'Return - Inventory reduction - Order: ' . $order->order_id,
            ],
        ];

        return $this->createEntry([
            'entry_date' => now(),
            'reference_type' => 'App\Models\Order',
            'reference_id' => $order->id,
            'description' => 'Return - COGS for sale',
        ], $cogsLines);
    }

    /**
     * Get balance sheet
     */
    public function getBalanceSheet($asOfDate = null)
    {
        $asOfDate = $asOfDate ?? now();

        $assets = Account::whereHas('accountType', function ($q) {
                $q->where('name', 'Asset');
            })
            ->where('is_active', true)
            ->get()
            ->sum('balance');

        $liabilities = Account::whereHas('accountType', function ($q) {
                $q->where('name', 'Liability');
            })
            ->where('is_active', true)
            ->get()
            ->sum('balance');

        $equity = Account::whereHas('accountType', function ($q) {
                $q->where('name', 'Equity');
            })
            ->where('is_active', true)
            ->get()
            ->sum('balance');

        return [
            'assets' => $assets,
            'liabilities' => abs($liabilities),
            'equity' => abs($equity),
            'total_liabilities_equity' => abs($liabilities) + abs($equity),
        ];
    }
}
