<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Product;
use App\Models\StockDetail;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Coustomer;
use App\Models\InventoryMovement;
use App\Models\AccountingEntry;
use App\Models\QRPayment;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Display the main ERP dashboard
     */
    public function index()
    {
        // Get current date range for calculations
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        $currentYear = Carbon::now()->startOfYear();
        
        // Dashboard Overview KPIs
        $kpis = $this->getDashboardKPIs($currentMonth, $previousMonth, $currentYear);
        
        // Recent Activities
        $recentOrders = $this->getRecentOrders();
        $lowStockAlerts = $this->getLowStockAlerts();
        $pendingOrders = $this->getPendingOrders();
        $recentInventoryMovements = $this->getRecentInventoryMovements();
        $pendingQRPayments = QRPayment::where('status', 'pending')->orderBy('created_at', 'desc')->take(5)->get();
        
        // Chart Data
        $salesChartData = $this->getSalesChartData();
        $inventoryChartData = $this->getInventoryChartData();
        $topProductsData = $this->getTopProductsData();
        
        return view('dashboard', compact(
            'kpis',
            'recentOrders',
            'lowStockAlerts',
            'pendingOrders',
            'recentInventoryMovements',
            'salesChartData',
            'inventoryChartData',
            'topProductsData',
            'pendingQRPayments'
        ));
    }

    /**
     * Get Dashboard KPIs
     */
    private function getDashboardKPIs($currentMonth, $previousMonth, $currentYear)
    {
        // Total Sales (Current Month) - using orders table
        $currentMonthSales = Order::whereHas('orderTracking', function($q) {
                $q->where('status', 3); // Delivered
            })
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->sum('orders.total');

        // Previous Month Sales for comparison
        $previousMonthSales = Order::whereHas('orderTracking', function($q) {
                $q->where('status', 3);
            })
            ->whereMonth('orders.created_at', Carbon::now()->subMonth()->month)
            ->sum('orders.total');

        // Current Stock Value - using product_inventories table
        $currentStockValue = \DB::table('product_inventories')
            ->where('quantity', '>', 0)
            ->sum(\DB::raw('quantity * COALESCE(buy_price, 0)'));

        // Pending Orders Count
        $pendingOrdersCount = Order::whereHas('orderTracking', function($q) {
                $q->where('status', 0); // Pending
            })->count();

        // Low Stock Items - using product_inventories table
        $lowStockCount = \DB::table('product_inventories')
            ->where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->count();

        // Monthly Profit Calculation
        $monthlyProfit = $this->calculateMonthlyProfit($currentMonth);

        // Orders Growth Rate
        $ordersGrowthRate = $this->calculateOrdersGrowthRate($currentMonth, $previousMonth);

        // Revenue Growth Rate
        $revenueGrowthRate = $previousMonthSales > 0
            ? (($currentMonthSales - $previousMonthSales) / $previousMonthSales) * 100
            : 0;



        return [
            'total_sales' => [
                'current' => $currentMonthSales,
                'growth_rate' => $revenueGrowthRate
            ],
            'current_stock_value' => $currentStockValue,
            'pending_orders' => $pendingOrdersCount,
            'low_stock_items' => $lowStockCount,
            'monthly_profit' => $monthlyProfit,
            'orders_growth_rate' => $ordersGrowthRate,
            'active_customers' => Coustomer::where('created_at', '>=', $currentMonth)->count(),
            'active_suppliers' => Supplier::where('status', 'active')->count()
        ];
    }

    /**
     * Get recent orders for dashboard
     */
    private function getRecentOrders()
    {
        $orders = Order::with(['customer', 'orderTracking'])
            ->latest()
            ->take(10)
            ->get();

        return $orders;
    }

    /**
     * Get low stock alerts
     */
    private function getLowStockAlerts()
    {
        $lowStockProducts = Product::join('product_inventories', 'products.id', '=', 'product_inventories.product_id')
            ->where('product_inventories.quantity', '<=', 10)
            ->where('product_inventories.quantity', '>', 0)
            ->select('products.*', 'product_inventories.quantity as stock_quantity')
            ->take(10)
            ->get();

        return $lowStockProducts;
    }

    /**
     * Get pending orders
     */
    private function getPendingOrders()
    {
        return OrderTracking::where('status', 1) // New/Processing
            ->with(['order.customer'])
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Get recent inventory movements
     */
    private function getRecentInventoryMovements()
    {
        return InventoryMovement::with(['product', 'user'])
            ->latest()
            ->take(15)
            ->get();
    }

    /**
     * Get sales chart data for dashboard
     */
    private function getSalesChartData()
    {
        // Last 12 months sales data
        $salesData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $sales = Order::whereHas('orderTracking', function($q) {
                    $q->where('status', 3); // Delivered
                })
                ->whereMonth('orders.created_at', $date->month)
                ->whereYear('orders.created_at', $date->year)
                ->sum('orders.total');

            $salesData[] = [
                'month' => $date->format('M Y'),
                'sales' => $sales
            ];
        }

        return $salesData;
    }

    /**
     * Get inventory chart data
     */
    private function getInventoryChartData()
    {
        // Top 10 products by stock value
        return Product::with(['stockDetails' => function($query) {
                $query->selectRaw('product_id, SUM(remaining_quantity * purchase_price) as total_value')
                      ->where('status', 'available')
                      ->where('remaining_quantity', '>', 0)
                      ->groupBy('product_id');
            }])
            ->whereHas('stockDetails', function($query) {
                $query->where('status', 'available')
                      ->where('remaining_quantity', '>', 0);
            })
            ->get()
            ->map(function($product) {
                return [
                    'product_name' => $product->product_name,
                    'stock_value' => $product->stockDetails->sum('total_value') ?? 0
                ];
            })
            ->sortByDesc('stock_value')
            ->take(10)
            ->values();
    }

    /**
     * Get top selling products
     */
    private function getTopProductsData()
    {
        $topProducts = Product::join('order_products', 'products.id', '=', 'order_products.product_id')
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->join('order_tracking', 'orders.id', '=', 'order_tracking.order_id')
            ->where('order_tracking.status', 3) // Delivered
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->selectRaw('products.product_name, SUM(order_products.quantity) as total_sold')
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();

        return $topProducts;
    }

    /**
     * Calculate monthly profit
     */
    private function calculateMonthlyProfit($startDate)
    {
        // This is a simplified calculation
        // In a real system, you'd have proper expense tracking
        $totalRevenue = Order::whereHas('orderTracking', function($q) {
                $q->where('status', 3); // Delivered
            })
            ->where('orders.created_at', '>=', $startDate)
            ->sum('orders.total');

        // Simplified COGS calculation (would be more complex in reality)
        $totalCOGS = DB::table('order_products')
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->join('order_tracking', 'orders.id', '=', 'order_tracking.order_id')
            ->where('order_tracking.status', 3)
            ->where('orders.created_at', '>=', $startDate)
            ->sum(DB::raw('COALESCE(order_products.cogs, 0) * order_products.quantity'));

        $profit = $totalRevenue - $totalCOGS;

        return $profit;
    }

    /**
     * Calculate orders growth rate
     */
    private function calculateOrdersGrowthRate($currentMonth, $previousMonth)
    {
        $currentOrders = Order::where('created_at', '>=', $currentMonth)->count();
        $previousOrders = Order::whereBetween('created_at', [
            $previousMonth,
            $currentMonth->subSecond()
        ])->count();
        
        return $previousOrders > 0
            ? (($currentOrders - $previousOrders) / $previousOrders) * 100
            : 0;
    }

    /**
     * API endpoint for real-time dashboard updates
     */
    public function getRealtimeData()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        
        return response()->json([
            'today_sales' => OrderTracking::where('status', 3)
                ->whereDate('order_tracking.created_at', Carbon::today())
                ->join('stripe_orders', 'order_tracking.order_id', '=', 'stripe_orders.id')
                ->sum('stripe_orders.total'),
                
            'today_orders' => OrderTracking::whereDate('created_at', Carbon::today())->count(),
            
            'pending_orders' => OrderTracking::where('status', 1)->count(),
            
            'low_stock_alerts' => Product::whereHas('stockDetails', function($query) {
                $query->select('product_id')->selectRaw('SUM(remaining_quantity) as total_stock')
                      ->groupBy('product_id')
                      ->havingRaw('SUM(remaining_quantity) < 10');
            })->count(),
            
            'monthly_profit' => $this->calculateMonthlyProfit($currentMonth)
        ]);
    }

    // API Methods for ERP Dashboard
    public function getKPIs()
    {
        $todaySales = Order::whereHas('orderTracking', function ($q) {
                $q->where('status', 3);
            })
            ->whereDate('created_at', Carbon::today())
            ->sum('total');

        $monthlySales = Order::whereHas('orderTracking', function ($q) {
                $q->where('status', 3);
            })
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total');

        $totalStock = ProductInventory::sum('quantity');

        $pnlData = $this->accountingService->getProfitLoss(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        $pendingOrders = Order::whereHas('orderTracking', function ($q) {
                $q->where('status', 0);
            })->count();

        $lowStockProducts = ProductInventory::where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->count();

        return response()->json([
            'today_sales' => $todaySales,
            'monthly_sales' => $monthlySales,
            'total_stock' => $totalStock,
            'net_profit' => $pnlData['net_profit'],
            'pending_orders' => $pendingOrders,
            'low_stock_products' => $lowStockProducts,
        ]);
    }

    public function getRecentMovements()
    {
        $movements = InventoryMovement::with('product')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'product_name' => $movement->product->product_name ?? 'Unknown',
                    'movement_type' => $movement->movement_type,
                    'quantity' => $movement->quantity,
                    'created_at' => $movement->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($movements);
    }

    public function getTopProducts()
    {
        $topProducts = Order::join('order_products', 'orders.order_id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('order_tracking', 'orders.id', '=', 'order_tracking.order_id')
            ->where('order_tracking.status', 3)
            ->select('products.product_name', \DB::raw('SUM(order_products.quantity) as sales_count'))
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'product_name' => $product->product_name,
                    'total_sold' => $product->sales_count,
                ];
            });

        return response()->json($topProducts);
    }
}
