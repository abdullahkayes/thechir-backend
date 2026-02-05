<?php

namespace App\Services;

use App\Models\StockDetail;
use App\Models\InventoryMovement;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FifoService
{
    /**
     * Process FIFO inventory deduction for sales
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity to deduct
     * @param string $referenceType Reference type (order, adjustment, etc.)
     * @param int $referenceId Reference ID
     * @return array Result with success status and details
     */
    public function deductInventory($productId, $quantity, $referenceType = 'sale', $referenceId = null)
    {
        try {
            DB::beginTransaction();
            
            $availableStock = $this->getAvailableStock($productId);
            $totalAvailable = $availableStock->sum('remaining_quantity');
            
            if ($totalAvailable < $quantity) {
                throw new \Exception("Insufficient stock. Available: {$totalAvailable}, Required: {$quantity}");
            }
            
            $remainingToDeduct = $quantity;
            $totalCost = 0;
            $deductedBatches = [];
            
            // Process FIFO: oldest stock first
            foreach ($availableStock->sortBy('created_at') as $batch) {
                if ($remainingToDeduct <= 0) break;
                
                $deductFromBatch = min($batch->remaining_quantity, $remainingToDeduct);
                
                // Update stock batch
                $batch->remaining_quantity -= $deductFromBatch;
                $batch->save();
                
                // Calculate cost for this deduction
                $batchCost = $deductFromBatch * $batch->purchase_price;
                $totalCost += $batchCost;
                
                // Record inventory movement
                InventoryMovement::create([
                    'product_id' => $productId,
                    'movement_type' => 'out',
                    'quantity' => $deductFromBatch,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'batch_id' => $batch->id,
                    'unit_cost' => $batch->purchase_price,
                    'total_cost' => $batchCost,
                    'user_id' => auth()->id(),
                    'notes' => "FIFO deduction for {$referenceType} #{$referenceId}"
                ]);
                
                $deductedBatches[] = [
                    'batch_id' => $batch->id,
                    'quantity_deducted' => $deductFromBatch,
                    'unit_cost' => $batch->purchase_price,
                    'total_cost' => $batchCost
                ];
                
                $remainingToDeduct -= $deductFromBatch;
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Inventory deducted successfully using FIFO',
                'data' => [
                    'total_quantity' => $quantity,
                    'total_cost' => $totalCost,
                    'average_cost' => $quantity > 0 ? $totalCost / $quantity : 0,
                    'deducted_batches' => $deductedBatches
                ]
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FIFO deduction failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Get available stock for a product using FIFO
     * 
     * @param int $productId Product ID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableStock($productId)
    {
        return StockDetail::where('product_id', $productId)
            ->where('status', 'available')
            ->where('remaining_quantity', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO: oldest first
            ->get();
    }
    
    /**
     * Get current stock value for a product
     * 
     * @param int $productId Product ID
     * @return array Stock information
     */
    public function getProductStockInfo($productId)
    {
        $stockBatches = $this->getAvailableStock($productId);
        
        $totalQuantity = $stockBatches->sum('remaining_quantity');
        $totalValue = $stockBatches->sum(function($batch) {
            return $batch->remaining_quantity * $batch->purchase_price;
        });
        
        $averageCost = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;
        
        return [
            'product_id' => $productId,
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'average_cost' => $averageCost,
            'batches' => $stockBatches->map(function($batch) {
                return [
                    'batch_id' => $batch->id,
                    'lot_number' => $batch->lot_number,
                    'expiry_date' => $batch->expiry_date,
                    'remaining_quantity' => $batch->remaining_quantity,
                    'purchase_price' => $batch->purchase_price,
                    'batch_value' => $batch->remaining_quantity * $batch->purchase_price,
                    'created_at' => $batch->created_at
                ];
            })
        ];
    }
    
    /**
     * Calculate COGS for order products using FIFO
     * 
     * @param int $orderId Order ID
     * @return array COGS calculation results
     */
    public function calculateOrderCogs($orderId)
    {
        $orderProducts = OrderProduct::where('order_id', $orderId)->get();
        $totalCogs = 0;
        $cogsDetails = [];
        
        foreach ($orderProducts as $orderProduct) {
            $fifoResult = $this->deductInventory(
                $orderProduct->product_id,
                $orderProduct->quantity,
                'order',
                $orderId
            );
            
            if ($fifoResult['success']) {
                $cogs = $fifoResult['data']['total_cost'];
                $orderProduct->cogs = $cogs;
                $orderProduct->cogs_per_unit = $fifoResult['data']['average_cost'];
                $orderProduct->save();
                
                $totalCogs += $cogs;
                
                $cogsDetails[] = [
                    'product_id' => $orderProduct->product_id,
                    'quantity' => $orderProduct->quantity,
                    'cogs' => $cogs,
                    'cogs_per_unit' => $fifoResult['data']['average_cost'],
                    'deducted_batches' => $fifoResult['data']['deducted_batches']
                ];
            }
        }
        
        return [
            'order_id' => $orderId,
            'total_cogs' => $totalCogs,
            'cogs_details' => $cogsDetails
        ];
    }
    
    /**
     * Process inventory return using FIFO reverse logic
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity to return
     * @param float $unitCost Unit cost of returned items
     * @param string $referenceType Reference type (return, adjustment, etc.)
     * @param int $referenceId Reference ID
     * @return array Result with success status and details
     */
    public function returnInventory($productId, $quantity, $unitCost, $referenceType = 'return', $referenceId = null)
    {
        try {
            DB::beginTransaction();
            
            // Create new stock batch for returned items
            $batch = StockDetail::create([
                'product_id' => $productId,
                'lot_number' => 'RTN-' . $referenceId . '-' . time(),
                'purchase_price' => $unitCost,
                'initial_quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'expiry_date' => now()->addYear(), // Default expiry for returns
                'status' => 'available',
                'supplier_id' => null, // Return doesn't have supplier
                'notes' => "Return from {$referenceType} #{$referenceId}"
            ]);
            
            // Record inventory movement
            InventoryMovement::create([
                'product_id' => $productId,
                'movement_type' => 'return',
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'batch_id' => $batch->id,
                'unit_cost' => $unitCost,
                'total_cost' => $quantity * $unitCost,
                'user_id' => auth()->id(),
                'notes' => "Return inventory for {$referenceType} #{$referenceId}"
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Inventory returned successfully',
                'data' => [
                    'batch_id' => $batch->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $quantity * $unitCost
                ]
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FIFO return failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Get inventory valuation using FIFO
     * 
     * @param array $productIds Optional array of product IDs to filter
     * @return array Inventory valuation data
     */
    public function getInventoryValuation($productIds = null)
    {
        $query = StockDetail::with('product')
            ->where('status', 'available')
            ->where('remaining_quantity', '>', 0);
            
        if ($productIds) {
            $query->whereIn('product_id', $productIds);
        }
        
        $stockBatches = $query->get();
        
        $valuation = [];
        $totalValue = 0;
        $totalQuantity = 0;
        
        foreach ($stockBatches->groupBy('product_id') as $productId => $batches) {
            $productTotalQuantity = $batches->sum('remaining_quantity');
            $productTotalValue = $batches->sum(function($batch) {
                return $batch->remaining_quantity * $batch->purchase_price;
            });
            
            $totalValue += $productTotalValue;
            $totalQuantity += $productTotalQuantity;
            
            $valuation[] = [
                'product_id' => $productId,
                'product_name' => $batches->first()->product->product_name ?? 'Unknown',
                'total_quantity' => $productTotalQuantity,
                'total_value' => $productTotalValue,
                'average_cost' => $productTotalQuantity > 0 ? $productTotalValue / $productTotalQuantity : 0,
                'batches' => $batches->map(function($batch) {
                    return [
                        'batch_id' => $batch->id,
                        'lot_number' => $batch->lot_number,
                        'quantity' => $batch->remaining_quantity,
                        'unit_cost' => $batch->purchase_price,
                        'batch_value' => $batch->remaining_quantity * $batch->purchase_price,
                        'expiry_date' => $batch->expiry_date
                    ];
                })
            ];
        }
        
        return [
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'average_cost' => $totalQuantity > 0 ? $totalValue / $totalQuantity : 0,
            'products' => $valuation
        ];
    }
    
    /**
     * Identify slow-moving inventory items
     * 
     * @param int $daysThreshold Days threshold for slow moving (default 90)
     * @return array Slow moving items
     */
    public function getSlowMovingItems($daysThreshold = 90)
    {
        $cutoffDate = now()->subDays($daysThreshold);
        
        $slowMovingStock = StockDetail::with('product')
            ->where('status', 'available')
            ->where('remaining_quantity', '>', 0)
            ->where('created_at', '<=', $cutoffDate)
            ->get();
            
        return $slowMovingStock->map(function($batch) {
            return [
                'product_id' => $batch->product_id,
                'product_name' => $batch->product->product_name ?? 'Unknown',
                'batch_id' => $batch->id,
                'lot_number' => $batch->lot_number,
                'quantity' => $batch->remaining_quantity,
                'unit_cost' => $batch->purchase_price,
                'batch_value' => $batch->remaining_quantity * $batch->purchase_price,
                'days_old' => now()->diffInDays($batch->created_at),
                'expiry_date' => $batch->expiry_date,
                'is_expired' => $batch->expiry_date && $batch->expiry_date < now()
            ];
        });
    }
    
    /**
     * Get expiring inventory items
     * 
     * @param int $daysThreshold Days threshold for expiry warning (default 30)
     * @return array Expiring items
     */
    public function getExpiringItems($daysThreshold = 30)
    {
        $expiryThreshold = now()->addDays($daysThreshold);
        
        $expiringStock = StockDetail::with('product')
            ->where('status', 'available')
            ->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '<=', $expiryThreshold)
            ->where('expiry_date', '>', now())
            ->orderBy('expiry_date', 'asc')
            ->get();
            
        return $expiringStock->map(function($batch) {
            $daysUntilExpiry = now()->diffInDays($batch->expiry_date, false);
            
            return [
                'product_id' => $batch->product_id,
                'product_name' => $batch->product->product_name ?? 'Unknown',
                'batch_id' => $batch->id,
                'lot_number' => $batch->lot_number,
                'quantity' => $batch->remaining_quantity,
                'unit_cost' => $batch->purchase_price,
                'batch_value' => $batch->remaining_quantity * $batch->purchase_price,
                'expiry_date' => $batch->expiry_date,
                'days_until_expiry' => $daysUntilExpiry,
                'urgency_level' => $daysUntilExpiry <= 7 ? 'critical' : ($daysUntilExpiry <= 30 ? 'warning' : 'normal')
            ];
        });
    }
}