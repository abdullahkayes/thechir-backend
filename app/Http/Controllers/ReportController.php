<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\InventoryMovement;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    // API Methods for Frontend Integration
    public function salesAnalytics()
    {
        $analytics = [
            'monthly_sales' => Order::join('order_tracking', 'orders.id', '=', 'order_tracking.order_id')
                ->where('order_tracking.status', 3)
                ->whereYear('orders.created_at', Carbon::now()->year)
                ->selectRaw('MONTH(orders.created_at) as month, SUM(orders.total) as sales')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => Carbon::create()->month($item->month)->format('F'),
                        'sales' => $item->sales,
                    ];
                }),

            'top_products' => Order::join('order_products', 'orders.id', '=', 'order_products.order_id')
                ->join('products', 'order_products.product_id', '=', 'products.id')
                ->join('order_tracking', 'orders.id', '=', 'order_tracking.order_id')
                ->where('order_tracking.status', 3)
                ->select('products.product_name', \DB::raw('SUM(order_products.quantity) as total_sold'))
                ->groupBy('products.id', 'products.product_name')
                ->orderBy('total_sold', 'desc')
                ->take(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'total_sold' => $item->total_sold,
                    ];
                }),

            'sales_by_category' => Order::join('order_products', 'orders.id', '=', 'order_products.order_id')
                ->join('products', 'order_products.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('order_tracking', 'orders.id', '=', 'order_tracking.order_id')
                ->where('order_tracking.status', 3)
                ->select('categories.category_name', \DB::raw('SUM(order_products.quantity * order_products.price) as revenue'))
                ->groupBy('categories.id', 'categories.category_name')
                ->orderBy('revenue', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'category_name' => $item->category_name,
                        'revenue' => $item->revenue,
                    ];
                }),
        ];

        return response()->json(['sales_analytics' => $analytics]);
    }

    public function inventoryReport()
    {
        $report = [
            'stock_levels' => \DB::table('product_inventories')
                ->join('products', 'product_inventories.product_id', '=', 'products.id')
                ->select('products.product_name', 'product_inventories.quantity as total_stock')
                ->orderBy('total_stock', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'total_stock' => $item->total_stock,
                        'status' => $item->total_stock <= 10 ? 'low' : 'normal',
                    ];
                }),

            'inventory_movements' => InventoryMovement::with('product:id,product_name')
                ->select('movement_type', \DB::raw('COUNT(*) as count'), \DB::raw('SUM(quantity) as total_quantity'))
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('movement_type')
                ->get()
                ->map(function ($item) {
                    return [
                        'movement_type' => $item->movement_type,
                        'count' => $item->count,
                        'total_quantity' => $item->total_quantity,
                    ];
                }),

            'expiry_alerts' => \DB::table('product_inventories')
                ->join('products', 'product_inventories.product_id', '=', 'products.id')
                ->whereNotNull('product_inventories.expiry_date')
                ->where('product_inventories.expiry_date', '<=', Carbon::now()->addDays(30))
                ->where('product_inventories.quantity', '>', 0)
                ->select('products.product_name', 'product_inventories.expiry_date', 'product_inventories.quantity as remaining_quantity')
                ->orderBy('product_inventories.expiry_date')
                ->get()
                ->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'expiry_date' => $item->expiry_date,
                        'remaining_quantity' => $item->remaining_quantity,
                        'days_until_expiry' => Carbon::now()->diffInDays(Carbon::parse($item->expiry_date)),
                    ];
                }),
        ];

        return response()->json(['inventory_report' => $report]);
    }

    public function profitLossReport()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $pnl = $this->accountingService->getProfitLoss($currentMonth, $endOfMonth);

        $report = [
            'period' => $currentMonth->format('F Y'),
            'revenue' => $pnl['revenue'],
            'cogs' => $pnl['cogs'],
            'gross_profit' => $pnl['gross_profit'],
            'operating_expenses' => $pnl['operating_expenses'],
            'net_profit' => $pnl['net_profit'],
            'profit_margin' => $pnl['revenue'] > 0 ? round(($pnl['net_profit'] / $pnl['revenue']) * 100, 2) : 0,
        ];

        return response()->json(['profit_loss_report' => $report]);
    }

    public function profitLoss(Request $request)
    {
        if ($request->has(['from_date', 'to_date'])) {
            $startDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
        } elseif ($request->period) {
            switch ($request->period) {
                case 'monthly':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'quarterly':
                    $startDate = Carbon::now()->startOfQuarter();
                    $endDate = Carbon::now()->endOfQuarter();
                    break;
                case 'yearly':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                default:
                    $startDate = Carbon::create(2020, 1, 1);
                    $endDate = Carbon::now();
            }
        } else {
            $startDate = Carbon::create(2020, 1, 1);
            $endDate = Carbon::now();
        }

        $orders = Order::with(['orderProducts.rel_to_product.productInventory'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return view('reports.profit-loss', compact('orders'));
    }

    public function sales()
    {
        $monthlySales = Order::whereYear('orders.created_at', Carbon::now()->year)
            ->selectRaw('MONTH(orders.created_at) as month, SUM(orders.total) as sales')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create()->month($item->month)->format('F'),
                    'sales' => $item->sales,
                ];
            });

        $topProducts = Order::join('order_products', 'orders.order_id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->select('products.product_name', \DB::raw('SUM(order_products.quantity) as total_sold'))
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'total_sold' => $item->total_sold,
                ];
            });

        $salesByCategory = Order::join('order_products', 'orders.order_id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.category_name', \DB::raw('SUM(order_products.quantity * order_products.price) as revenue'))
            ->groupBy('categories.id', 'categories.category_name')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category_name,
                    'revenue' => $item->revenue,
                ];
            });

        $analytics = [
            'monthly_sales' => $monthlySales,
            'top_products' => $topProducts,
            'sales_by_category' => $salesByCategory,
        ];

        return view('reports.sales', compact('analytics'));
    }

    public function inventory()
    {
        // Get inventory valuation data
        $inventoryValuation = \DB::table('product_inventories')
            ->join('products', 'product_inventories.product_id', '=', 'products.id')
            ->leftJoin('inventory_movements', function($join) {
                $join->on('inventory_movements.product_id', '=', 'product_inventories.product_id')
                     ->whereRaw('inventory_movements.id = (SELECT MAX(id) FROM inventory_movements im WHERE im.product_id = product_inventories.product_id)');
            })
            ->where('product_inventories.quantity', '>', 0)
            ->selectRaw('
                products.product_name,
                "" as sku,
                product_inventories.quantity as current_stock,
                COALESCE(product_inventories.buy_price, 0) as average_cost,
                (product_inventories.quantity * COALESCE(product_inventories.buy_price, 0)) as total_value,
                COALESCE(inventory_movements.created_at, NULL) as last_movement,
                CASE WHEN product_inventories.quantity <= 10 THEN "slow" ELSE "active" END as status
            ')
            ->orderBy('total_value', 'desc')
            ->get();

        // Calculate summary values
        $totalInventoryValue = $inventoryValuation->sum('total_value');
        $totalProducts = $inventoryValuation->count();
        $slowMovingCount = $inventoryValuation->where('status', 'slow')->count();

        // Get expiry alerts
        $expiringSoon = \DB::table('product_inventories')
            ->join('products', 'product_inventories.product_id', '=', 'products.id')
            ->whereNotNull('product_inventories.expiry_date')
            ->where('product_inventories.expiry_date', '<=', Carbon::now()->addDays(30))
            ->where('product_inventories.expiry_date', '>=', Carbon::now())
            ->where('product_inventories.quantity', '>', 0)
            ->select('products.product_name', 'product_inventories.expiry_date', 'product_inventories.quantity')
            ->orderBy('product_inventories.expiry_date')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'product_name' => $item->product_name,
                    'lot_number' => 'N/A',
                    'days_until_expiry' => Carbon::now()->diffInDays(Carbon::parse($item->expiry_date)),
                ];
            });

        $expiredItems = \DB::table('product_inventories')
            ->join('products', 'product_inventories.product_id', '=', 'products.id')
            ->whereNotNull('product_inventories.expiry_date')
            ->where('product_inventories.expiry_date', '<', Carbon::now())
            ->where('product_inventories.quantity', '>', 0)
            ->select('products.product_name', 'product_inventories.expiry_date', 'product_inventories.quantity')
            ->orderBy('product_inventories.expiry_date')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'product_name' => $item->product_name,
                    'lot_number' => 'N/A',
                    'days_expired' => Carbon::parse($item->expiry_date)->diffInDays(Carbon::now()),
                ];
            });

        // Simple calculations for the complex metrics (placeholders)
        $inventoryTurnoverRatio = 0; // Would need sales data to calculate properly
        $daysSalesOfInventory = 0;
        $stockToSalesRatio = 0;

        // Placeholder data for charts and complex features
        $stockMovementData = collect([]); // Monthly stock movement data
        $stockAgeDistribution = [0, 0, 0, 0]; // Age distribution
        $slowMovingItems = collect([]);
        $reorderRecommendations = collect([]);
        $overstockItems = collect([]);
        $fastMovingItems = collect([]);

        return view('reports.inventory', compact(
            'totalInventoryValue',
            'totalProducts',
            'slowMovingCount',
            'inventoryValuation',
            'inventoryTurnoverRatio',
            'daysSalesOfInventory',
            'stockToSalesRatio',
            'slowMovingItems',
            'expiringSoon',
            'expiredItems',
            'stockMovementData',
            'stockAgeDistribution',
            'reorderRecommendations',
            'overstockItems',
            'fastMovingItems'
        ));
    }
}
