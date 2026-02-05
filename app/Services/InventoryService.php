<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\StockDetail;
use App\Models\InventoryMovement;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Process stock IN from purchase order
     */
    public function receiveStock($purchaseOrderItem, $quantity, $lotNumber = null, $expiryDate = null)
    {
        DB::beginTransaction();
        try {
            // Create stock detail entry
            $stockDetail = StockDetail::create([
                'product_id' => $purchaseOrderItem->product_id,
                'purchase_order_id' => $purchaseOrderItem->purchase_order_id,
                'lot_number' => $lotNumber ?? 'LOT-' . date('Ymd') . '-' . uniqid(),
                'purchase_price' => $purchaseOrderItem->unit_cost,
                'quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'expiry_date' => $expiryDate,
                'received_date' => now(),
                'status' => 'available',
            ]);

            // Log inventory movement
            InventoryMovement::create([
                'product_id' => $purchaseOrderItem->product_id,
                'stock_detail_id' => $stockDetail->id,
                'movement_type' => 'IN',
                'quantity' => $quantity,
                'unit_cost' => $purchaseOrderItem->unit_cost,
                'total_value' => $quantity * $purchaseOrderItem->unit_cost,
                'reference_type' => 'App\Models\PurchaseOrder',
                'reference_id' => $purchaseOrderItem->purchase_order_id,
                'notes' => 'Stock received from PO',
            ]);

            // Update purchase order item
            $purchaseOrderItem->received_quantity += $quantity;
            $purchaseOrderItem->save();

            DB::commit();
            return $stockDetail;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process stock OUT from ProductInventory
     */
    public function deductStock($productId, $quantity, $referenceType = null, $referenceId = null)
    {
        DB::beginTransaction();
        try {
            // Find the product inventory record
            $productInventory = ProductInventory::where('product_id', $productId)->first();

            if (!$productInventory) {
                throw new \Exception('Product inventory not found');
            }

            if ($productInventory->quantity < $quantity) {
                throw new \Exception('Insufficient stock available');
            }

            // Calculate cost (use buy_price if available, otherwise use 0)
            $unitCost = $productInventory->buy_price ?? 0;
            $totalCost = $quantity * $unitCost;

            // Update inventory quantity
            $productInventory->quantity -= $quantity;
            $productInventory->save();

            // Log movement
            $movement = InventoryMovement::create([
                'product_id' => $productId,
                'movement_type' => 'OUT',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_value' => $totalCost,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            DB::commit();
            return [
                'movements' => [$movement],
                'total_cost' => $totalCost,
                'average_cost' => $unitCost,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process stock return
     */
    public function returnStock($productId, $quantity, $referenceType = null, $referenceId = null)
    {
        DB::beginTransaction();
        try {
            // Find the product inventory record
            $productInventory = ProductInventory::where('product_id', $productId)->first();

            if (!$productInventory) {
                throw new \Exception('Product inventory not found');
            }

            // Add back to inventory
            $productInventory->quantity += $quantity;
            $productInventory->save();

            // Get the cost from recent OUT movements for this reference
            $recentOutMovement = InventoryMovement::where('product_id', $productId)
                ->where('movement_type', 'OUT')
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->orderBy('created_at', 'desc')
                ->first();

            $unitCost = $recentOutMovement ? $recentOutMovement->unit_cost : 0;

            // Log return movement
            InventoryMovement::create([
                'product_id' => $productId,
                'movement_type' => 'RETURN',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_value' => $quantity * $unitCost,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Adjust stock (damage, missing, etc.)
     */
    public function adjustStock($productId, $quantity, $type, $reason, $notes = null)
    {
        DB::beginTransaction();
        try {
            $productInventory = ProductInventory::where('product_id', $productId)->first();

            if (!$productInventory) {
                throw new \Exception('Product inventory not found');
            }

            if ($type === 'DAMAGE' || $type === 'MISSING') {
                // Deduct from inventory
                if ($productInventory->quantity < abs($quantity)) {
                    throw new \Exception('Insufficient stock for adjustment');
                }
                $productInventory->quantity -= abs($quantity);
                $productInventory->save();
            }

            // Log adjustment
            InventoryMovement::create([
                'product_id' => $productId,
                'movement_type' => $type,
                'quantity' => abs($quantity),
                'reason' => $reason,
                'notes' => $notes,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get current stock level for a product
     */
    public function getCurrentStock($productId)
    {
        return ProductInventory::where('product_id', $productId)->sum('quantity');
    }

    /**
     * Get stock valuation
     */
    public function getStockValuation($productId = null)
    {
        $query = ProductInventory::where('quantity', '>', 0);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->get()->sum(function ($inventory) {
            return $inventory->quantity * ($inventory->buy_price ?? 0);
        });
    }

    /**
     * Get expiring stock
     */
    public function getExpiringStock($days = 30)
    {
        return StockDetail::where('status', 'available')
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->whereDate('expiry_date', '>=', now())
            ->with('product')
            ->get();
    }

    /**
     * Get expired stock
     */
    public function getExpiredStock()
    {
        return StockDetail::where('status', 'available')
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now())
            ->with('product')
            ->get();
    }
}
