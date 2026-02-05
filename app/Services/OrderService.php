<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\InventoryMovement;
use App\Models\ProductInventory;
use App\Models\StockDetail;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $inventoryService;
    protected $accountingService;

    public function __construct(InventoryService $inventoryService, AccountingService $accountingService)
    {
        $this->inventoryService = $inventoryService;
        $this->accountingService = $accountingService;
    }

    /**
     * Process order fulfillment (deduct inventory and record accounting)
     */
    public function fulfillOrder(Order $order)
    {
        // Validate order status
        if ($order->isCompleted()) {
            throw new \Exception('Order is already fulfilled');
        }

        DB::beginTransaction();
        try {
            $totalCogs = 0;

            foreach ($order->orderProducts as $orderProduct) {
                // Check if product exists and has stock
                $currentStock = $this->inventoryService->getCurrentStock($orderProduct->product_id);
                $productName = $orderProduct->product ? $orderProduct->product->product_name : 'Unknown';
                if ($currentStock < $orderProduct->quantity) {
                    throw new \Exception("Insufficient stock for product: {$productName}");
                }

                // Deduct inventory using FIFO
                $deduction = $this->inventoryService->deductStock(
                    $orderProduct->product_id,
                    $orderProduct->quantity,
                    'App\Models\Order',
                    $order->id
                );

                $totalCogs += $deduction['total_cost'];

                // Update order product with COGS
                $orderProduct->cogs = $deduction['average_cost'];
                $orderProduct->save();
            }

            // Record accounting entries
            $this->accountingService->recordSale($order, $totalCogs);

            DB::commit();
            return ['success' => true, 'cogs' => $totalCogs];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order fulfillment failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'order_products' => $order->orderProducts->pluck('product_id')->toArray()
            ]);
            throw $e;
        }
    }

    /**
     * Process order return
     */
    public function processReturn(Order $order, array $returnItems)
    {
        DB::beginTransaction();
        try {
            $totalRefund = 0;
            $totalCogsRefund = 0;

            foreach ($returnItems as $returnItem) {
                $orderProduct = OrderProduct::findOrFail($returnItem['order_product_id']);
    
                // Validate quantity
                if ($returnItem['quantity'] <= 0) {
                    throw new \Exception('Return quantity must be greater than 0');
                }
    
                // Validate that we're not returning more than was ordered
                if ($returnItem['quantity'] > $orderProduct->quantity) {
                    throw new \Exception('Cannot return more than was originally ordered');
                }
    
                if ($returnItem['type'] === 'resellable') {
                    // Return to inventory
                    try {
                        $this->inventoryService->returnStock(
                            $orderProduct->product_id,
                            $returnItem['quantity'],
                            'App\Models\Order',
                            $order->id
                        );
                    } catch (\Exception $e) {
                        throw new \Exception('Failed to return stock for product ' . ($orderProduct->product->product_name ?? 'Unknown') . ': ' . $e->getMessage());
                    }
    
                    $totalRefund += $orderProduct->price * $returnItem['quantity'];
                    $totalCogsRefund += $orderProduct->cogs * $returnItem['quantity'];
                } else {
                    // Damaged - just log as loss
                    try {
                        InventoryMovement::create([
                            'product_id' => $orderProduct->product_id,
                            'movement_type' => 'DAMAGE',
                            'quantity' => $returnItem['quantity'],
                            'reason' => 'Customer return - damaged',
                            'reference_type' => 'App\Models\Order',
                            'reference_id' => $order->id,
                        ]);
    
                        // Record loss in accounting
                        $this->accountingService->recordLoss(
                            $orderProduct->product_id,
                            $orderProduct->cogs * $returnItem['quantity'],
                            'Damaged return'
                        );
                    } catch (\Exception $e) {
                        throw new \Exception('Failed to record damaged return for product ' . ($orderProduct->product->product_name ?? 'Unknown') . ': ' . $e->getMessage());
                    }
                }
            }

            // Reverse accounting entries
            try {
                $this->accountingService->reverseSale($order, $totalCogsRefund);
            } catch (\Exception $e) {
                throw new \Exception('Failed to reverse accounting entries: ' . $e->getMessage());
            }

            DB::commit();
            return ['success' => true, 'refund' => $totalRefund, 'cogs_refund' => $totalCogsRefund];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get order COGS
     */
    public function calculateOrderCogs(Order $order)
    {
        $totalCogs = 0;

        foreach ($order->orderProducts as $orderProduct) {
            // Get cost from ProductInventory
            $productInventory = ProductInventory::where('product_id', $orderProduct->product_id)->first();

            if ($productInventory) {
                $unitCost = $productInventory->buy_price ?? 0;
                $cogs = $orderProduct->quantity * $unitCost;
                $orderProduct->cogs = $unitCost;
                $orderProduct->save();
                $totalCogs += $cogs;
            } else {
                // No inventory record found, set COGS to 0
                $orderProduct->cogs = 0;
                $orderProduct->save();
            }
        }

        return $totalCogs;
    }
}