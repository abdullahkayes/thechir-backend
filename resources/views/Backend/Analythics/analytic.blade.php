@extends('layouts.admin')
@section('content')
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Independent Analytics — Premium Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Sora:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #1a1f3a;
      --primary-dark: #0f1419;
      --accent: #00d4ff;
      --bg-dark: #0a0e1a;
      --bg-card: #111827;
      --text-primary: #e8eef5;
      --text-secondary: #9ca3af;
      --border-color: #1e293b;
      --highlight: #2dd4bf;
    }

    * { transition: all 0.3s ease; }

    html, body { height: 100%; }

    body {
      font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto;
      background: var(--bg-dark);
      color: var(--text-primary);
      overflow-x: hidden;
    }

    .glass-effect {
      background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(45, 212, 191, 0.1);
    }

    .stat-card {
      background: linear-gradient(135deg, rgba(17, 24, 39, 0.8) 0%, rgba(20, 28, 45, 0.6) 100%);
      border: 1px solid rgba(45, 212, 191, 0.15);
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(45, 212, 191, 0.3), transparent);
    }

    .stat-card:hover {
      border-color: rgba(45, 212, 191, 0.4);
      background: linear-gradient(135deg, rgba(20, 28, 45, 0.9) 0%, rgba(25, 35, 55, 0.8) 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(45, 212, 191, 0.08);
    }

    .chart-container {
      background: linear-gradient(135deg, rgba(17, 24, 39, 0.7) 0%, rgba(20, 28, 45, 0.5) 100%);
      border: 1px solid rgba(45, 212, 191, 0.12);
    }

    .btn-primary {
      background: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 100%);
      box-shadow: 0 0 20px rgba(45, 212, 191, 0.2);
      color: #0a0e1a;
    }

    .btn-primary:hover {
      box-shadow: 0 0 30px rgba(45, 212, 191, 0.35);
      transform: translateY(-1px);
      background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    }

    .top-bar {
      background: linear-gradient(90deg, #111827 0%, #0f1419 100%);
      border-bottom: 1px solid rgba(45, 212, 191, 0.15);
      box-shadow: 0 10px 40px rgba(45, 212, 191, 0.08);
    }

    .header-glow {
      position: relative;
      z-index: 1;
    }

    .metric-badge {
      background: rgba(45, 212, 191, 0.15);
      border: 1px solid rgba(45, 212, 191, 0.4);
      color: #2dd4bf;
    }

    .table-row:hover {
      background: rgba(45, 212, 191, 0.08);
      border-left: 3px solid #2dd4bf;
    }

    .scroll-smooth { scroll-behavior: smooth; }

    .gradient-text {
      background: linear-gradient(135deg, #2dd4bf 0%, #00d4ff 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .loading-bar {
      height: 3px;
      background: linear-gradient(90deg, #2dd4bf, #00d4ff, #2dd4bf);
      background-size: 200% 100%;
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }

    .filter-tag {
      background: rgba(45, 212, 191, 0.15);
      border: 1px solid rgba(45, 212, 191, 0.4);
      color: #5eead4;
    }

    .text-accent {
      color: #2dd4bf;
    }

    .text-secondary {
      color: var(--text-secondary);
    }

    .text-positive {
      color: #10b981;
    }

    .text-negative {
      color: #ef4444;
    }
  </style>
</head>
<body class="scroll-smooth">

  <!-- TOP BAR -->
  <header class="top-bar text-white px-8 py-4 sticky top-0 z-50">
    <div class="flex items-center justify-between header-glow">
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <div class="w-10 h-10 rounded-lg bg-teal-500/20 flex items-center justify-center">
            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <h1 class="text-2xl font-bold font-['Sora'] text-white">independent <span class="font-light opacity-70 text-gray-400">analytics</span></h1>
        </div>
        <div class="metric-badge px-3 py-1 rounded-full text-xs font-semibold">● Live</div>
      </div>
      <div class="flex items-center gap-4">
        <div class="text-sm opacity-80 text-gray-300">Howdy, Dazzling ▾</div>
      </div>
    </div>
  </header>

  <main class="flex-1 overflow-auto scroll-smooth">
    <div class="px-8 py-8 max-w-7xl mx-auto">

      <!-- Page Header -->
      <div class="mb-8">
        <h2 class="text-4xl font-bold font-['Sora'] text-black mb-2">Pages Overview</h2>
        <p class="text-gray-400">Real-time analytics and performance insights</p>
      </div>

      <!-- Controls Bar -->
    
 <div class="flex items-center justify-end pb-4 pt-2 gap-4">

              <a href="{{ route('analytics.export') }}" class="text-teal-400 hover:text-teal-300 text-sm font-medium">↓ Download Selles Report To Excel</a>
       </div>
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="stat-card rounded-xl p-6">
          <div class="text-gray-500 text-sm font-medium mb-2">Visitors</div>
          <div class="text-3xl font-bold mb-2 text-white">{{ $visitors }}</div>
        </div>

        <div class="stat-card rounded-xl p-6">
          <div class="text-gray-500 text-sm font-medium mb-2">Views</div>
          <div class="text-3xl font-bold mb-2 text-white">{{ $views }}</div>
        </div>

        <div class="stat-card rounded-xl p-6">
          <div class="text-gray-500 text-sm font-medium mb-2">Sessions</div>
          <div class="text-3xl font-bold mb-2 text-white">{{ $sessions }}</div>
        </div>

        <div class="stat-card rounded-xl p-6">
          <div class="text-gray-500 text-sm font-medium mb-2">Total Sales</div>
         
          <div class="text-3xl font-bold mb-2 text-white">{{ $totalSales }}</div>
         
        </div>

        <div class="stat-card rounded-xl p-6">
          <div class="text-gray-500 text-sm font-medium mb-2">Total Pages</div>
          <div class="text-3xl font-bold mb-2 text-white">{{ $viewsPerSession }}</div>
        </div>
      </div>

      <!-- Chart Card -->
      <div class="glass-effect rounded-xl p-6 mb-8 chart-container">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-lg font-semibold text-white">Sales Chart</h3>
            <p class="text-gray-500 text-sm">Visitors & page views over time</p>
          </div>
          <div class="flex items-center gap-3">
           
          </div>
        </div>
        <canvas id="trafficChart" height="100"></canvas>
     
      </div>

      <!-- Table Section -->
      <div class="glass-effect rounded-xl overflow-hidden chart-container">
        <div class="p-6 border-b border-gray-700/50">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Top Pages</h3>
           
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-900/50 border-b border-gray-700/50">
              <tr>
                <th class="py-4 px-6 text-left font-semibold text-gray-300">#</th>
                <th class="py-4 px-6 text-left font-semibold text-gray-300">Title</th>
                <th class="py-4 px-6 text-left font-semibold text-gray-300">Visitors</th>
                <th class="py-4 px-6 text-left font-semibold text-gray-300">Views</th>
                <th class="py-4 px-6 text-left font-semibold text-gray-300">URL</th>
              </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-gray-700/50">
              @php
              $index = 0;
              @endphp
              @foreach($topPages as $page)
                  @php
                  $index++;
                  @endphp
              <tr class="table-row">
                    <td class="py-4 px-6 text-gray-400">{{ $index }}</td>
                    <td class="py-4 px-6 font-medium text-gray-200">{{ $page->page_title ?? 'N/A' }}</td>
                    <td class="py-4 px-6 visitors-col text-gray-300">{{ $page->visitors ?? 'N/A' }}</td>
                    <td class="py-4 px-6 views-col text-gray-300">{{ $page->views }}</td>
                <td class="py-4 px-6 text-teal-400 font-medium">http://localhost:5173{{ $page->page_url }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

  
    </div>

      <div class="mt-8 text-xs text-gray-600 text-center">* Premium replica with enhanced visuals and smooth interactions</div>

    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('trafficChart').getContext('2d');
    const labels = @json($salesLabels);
    const visitors = @json($visitorsValues);
    const views = @json($viewsValues);
    const sales = @json($salesValues);

    const trafficChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          { label: 'Visitors', data: visitors, borderColor: '#2dd4bf', backgroundColor: 'rgba(45, 212, 191, 0.08)', tension: 0.4, pointRadius: 0, fill: true, borderWidth: 2 },
          { label: 'Views', data: views, borderColor: '#00d4ff', backgroundColor: 'rgba(0, 212, 255, 0.08)', tension: 0.4, pointRadius: 0, fill: true, borderWidth: 2 },
          { label: 'Sales', data: sales, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.08)', tension: 0.4, pointRadius: 0, fill: true, borderWidth: 2 }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(75, 85, 99, 0.1)' }, ticks: { color: '#9ca3af' } },
          x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
        }
      }
    });

    document.getElementById('toggleCols').addEventListener('click', function() {
      const visitorsCols = document.querySelectorAll('.visitors-col');
      const viewsCols = document.querySelectorAll('.views-col');
      const durationCols = document.querySelectorAll('.duration-col');
      const bounceCols = document.querySelectorAll('.bounce-col');

      visitorsCols.forEach(col => col.classList.toggle('hidden'));
      viewsCols.forEach(col => col.classList.toggle('hidden'));
      durationCols.forEach(col => col.classList.toggle('hidden'));
      bounceCols.forEach(col => col.classList.toggle('hidden'));
    });
  </script>
</body>
</html>
@endsection