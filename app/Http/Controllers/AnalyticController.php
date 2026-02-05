<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analytic;
use App\Models\Order; // Import the Order model
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; // Import the Excel facade
use App\Exports\AnalyticsExport; // Import the AnalyticsExport class

class AnalyticController extends Controller
{
    public function index()
    {
        // Visitors: Unique IP addresses
        $visitors = Analytic::distinct('ip_address')->count('ip_address');

        // Views: Total records
        $views = Analytic::count();

        // Sessions: Unique session IDs
        $sessions = Analytic::distinct('session_id')->count('session_id');

        // Average Session Duration
        $avgDurationResult = Analytic::select(DB::raw('AVG(time_on_page) as avg_duration'))->first();
        $avgDuration = $avgDurationResult ? round($avgDurationResult->avg_duration) : 0;

        // Bounce Rate: Sessions with only one page view
        $bounces = Analytic::select('session_id')
            ->groupBy('session_id')
            ->havingRaw('COUNT(id) = 1')
            ->get()->count();
        $bounceRate = $sessions > 0 ? round(($bounces / $sessions) * 100, 2) : 0;

        // Views per Session
        $viewsPerSession = $sessions > 0 ? round($views / $sessions, 2) : 0;

        // Top Pages (from analytics table)
        $topPages = Analytic::select(
            'page_title',
            'page_url',
            DB::raw('count(*) as views'),
            DB::raw('count(distinct ip_address) as visitors'),
            DB::raw('avg(time_on_page) as avg_duration'),
            DB::raw('count(CASE WHEN time_on_page IS NULL THEN 1 END) as bounce_rate')
        )
        ->groupBy('page_title')
        ->groupBy('page_url')
        ->orderByDesc('views')
        ->get();

        // Count total sales
        $totalSales = Order::count(); // Count total orders since page_url is not required

        // Calculate sales for each top page (if needed)
        foreach ($topPages as $page) {
            $page->sales = $totalSales; // Replace this logic if needed
        }

        // Sales data for chart (last 30 days)
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();
        $salesData = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as sales'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Visitors data for chart (last 30 days)
        $visitorsData = Analytic::select(DB::raw('DATE(visit_time) as date'), DB::raw('count(distinct ip_address) as visitors'))
            ->whereBetween('visit_time', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Views data for chart (last 30 days)
        $viewsData = Analytic::select(DB::raw('DATE(visit_time) as date'), DB::raw('count(*) as views'))
            ->whereBetween('visit_time', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $salesLabels = [];
        $salesValues = [];
        $visitorsValues = [];
        $viewsValues = [];
        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays(29 - $i)->format('Y-m-d');
            $salesLabels[] = $date;
            $sale = $salesData->where('date', $date)->first();
            $salesValues[] = $sale ? $sale->sales : 0;
            $visitor = $visitorsData->where('date', $date)->first();
            $visitorsValues[] = $visitor ? $visitor->visitors : 0;
            $view = $viewsData->where('date', $date)->first();
            $viewsValues[] = $view ? $view->views : 0;
        }

        return view('Backend.Analythics.analytic', compact(
            'visitors',
            'views',
            'sessions',
            'avgDuration',
            'bounceRate',
            'viewsPerSession',
            'topPages',
            'salesLabels',
            'salesValues',
            'visitorsValues',
            'totalSales',
            'viewsValues'
        ));
    }

    public function export()
    {
        return Excel::download(new AnalyticsExport, 'analytics.xlsx');
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'page_title' => 'required|string',
            'time_on_page' => 'nullable|integer',
        ]);

        Analytic::create([
            'session_id' => $validated['session_id'],
            'page_title' => $validated['page_title'],
            'ip_address' => $request->ip(),
            'country' => $request->input('country'),
            'user_agent' => $request->userAgent(),
            'visit_time' => now(),
            'time_on_page' => $validated['time_on_page'] ?? null,
        ]);

        return response()->json(['status' => 'success']);
    }
}
