<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\B2b;
use App\Models\Distributer;
use App\Models\Commission;
use App\Models\Order;
use App\Models\PayoutRequest;
use App\Models\PaymentUpdate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CommissionExport;
use App\Exports\BuyReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminResellerController extends Controller
{
    public function index()
    {
        $pendingResellers = Reseller::where('status', 'pending')->get();
        $pendingB2b = B2b::where('status', 'pending')->get();
        $pendingAmazon = \App\Models\Amazon::where('status', 'pending')->get();
        $pendingDistributers = Distributer::where('status', 'pending')->get();
        $pendingPayoutRequests = PayoutRequest::where('status', 'pending')
            ->with('reseller')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingPaymentUpdates = PaymentUpdate::where('status', 'pending')
            ->whereHas('order') // Only include PaymentUpdate records with existing orders
            ->whereHas('amazon') // Only include PaymentUpdate records with existing Amazon users
            ->with(['order', 'amazon'])
            ->orderBy('created_at', 'desc')
            ->get();

        $resellers = Reseller::with(['commissions', 'orders'])->get();
        $b2bs = B2b::with(['orders'])->get();
        $amazons = \App\Models\Amazon::with(['orders'])->get();
        $distributers = Distributer::with(['orders'])->get();

        // Get all reseller orders
        $resellerOrders = \App\Models\Order::with(['customer', 'reseller', 'orderProducts.product'])
            ->whereNotNull('reseller_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all B2B orders
        $b2bOrders = \App\Models\Order::with(['customer', 'b2b', 'orderProducts.product'])
            ->whereNotNull('b2b_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all Amazon orders
        $amazonOrders = \App\Models\Order::with(['customer', 'amazon', 'orderProducts.product', 'paymentUpdates'])
            ->whereNotNull('amazon_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all distributer orders
        $distributerOrders = \App\Models\Order::with(['customer', 'distributer', 'orderProducts.product'])
            ->whereNotNull('distributer_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all B2B users with full details
        $allB2bUsers = B2b::orderBy('created_at', 'desc')->get();

        // Get all Amazon users with full details
        $allAmazonUsers = \App\Models\Amazon::orderBy('created_at', 'desc')->get();

        // Get all distributer users with full details
        $allDistributerUsers = Distributer::orderBy('created_at', 'desc')->get();

        return view('Backend.reseller-dashboard', compact('pendingResellers', 'pendingB2b', 'pendingAmazon', 'pendingDistributers', 'pendingPayoutRequests', 'pendingPaymentUpdates', 'resellers', 'b2bs', 'amazons', 'distributers', 'resellerOrders', 'b2bOrders', 'amazonOrders', 'distributerOrders', 'allB2bUsers', 'allAmazonUsers', 'allDistributerUsers'));
    }

    public function approveB2b(Request $request, $id)
    {
        $b2b = B2b::findOrFail($id);
        $b2b->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'B2B approved');
    }

    public function rejectB2b(Request $request, $id)
    {
        $b2b = B2b::findOrFail($id);
        $b2b->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'B2B rejected');
    }

    public function approveAmazon(Request $request, $id)
    {
        $amazon = \App\Models\Amazon::findOrFail($id);
        $amazon->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Amazon user approved');
    }

    public function rejectAmazon(Request $request, $id)
    {
        $amazon = \App\Models\Amazon::findOrFail($id);
        $amazon->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Amazon user rejected');
    }

    public function approveReseller(Request $request, $id)
    {
        $reseller = Reseller::findOrFail($id);
        $reseller->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Reseller approved');
    }

    public function rejectReseller(Request $request, $id)
    {
        $reseller = Reseller::findOrFail($id);
        $reseller->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Reseller rejected');
    }

    public function approveDistributer(Request $request, $id)
    {
        $distributer = Distributer::findOrFail($id);
        $distributer->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Distributer approved');
    }

    public function rejectDistributer(Request $request, $id)
    {
        $distributer = Distributer::findOrFail($id);
        $distributer->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Distributer rejected');
    }

    public function resellers()
    {
        $resellers = Reseller::with(['commissions', 'orders'])->get();

        return response()->json($resellers);
    }

    public function b2bs()
    {
        $b2bs = B2b::with(['orders'])->get();

        return response()->json($b2bs);
    }

    public function amazons()
    {
        $amazons = \App\Models\Amazon::with(['orders'])->get();

        return response()->json($amazons);
    }

    public function distributers()
    {
        $distributers = Distributer::with(['orders'])->get();

        return response()->json($distributers);
    }

    public function commissions()
    {
        $commissions = Commission::with(['reseller', 'order'])->get();
        return response()->json($commissions);
    }

    public function downloadInvoice($orderId)
    {
        $order = Order::with(['orderProducts.product', 'customer', 'reseller', 'b2b'])
            ->where('order_id', $orderId)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $billing = \App\Models\Billing::where('order_id', $orderId)->first();

        $data = (object) [
            'order_id' => $orderId,
            'order' => $order,
            'billing' => $billing
        ];

        try {
            $pdf = Pdf::loadView('pdf.invoice', compact('data'));
            return $pdf->download('invoice_' . $orderId . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    public function viewResaleCertificate($b2bId)
    {
        $b2b = B2b::findOrFail($b2bId);

        if (!$b2b->resale_certificate_path) {
            abort(404, 'No resale certificate found');
        }

        $filePath = null;

        // Check if it's a full path starting with upload/
        if (str_starts_with($b2b->resale_certificate_path, 'upload/')) {
            $filePath = public_path($b2b->resale_certificate_path);
        }
        // Check if it's a Laravel storage path (old method)
        elseif (str_contains($b2b->resale_certificate_path, '/')) {
            $filePath = storage_path('app/' . $b2b->resale_certificate_path);
        }
        // Check if it's just a filename (old method)
        else {
            // Try public directory first (new method)
            $publicPath = public_path('upload/resale_certificates/' . $b2b->resale_certificate_path);
            if (file_exists($publicPath)) {
                $filePath = $publicPath;
            } else {
                // Try storage directory (old method)
                $storagePath = storage_path('app/private/resale_certificates/' . $b2b->resale_certificate_path);
                if (file_exists($storagePath)) {
                    $filePath = $storagePath;
                }
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'File not found');
        }

        // Create a simple HTML page that embeds the file for viewing
        $mimeType = mime_content_type($filePath);
        $filename = basename($filePath);

        // Generate correct URL for the file
        $relativePath = str_replace(public_path(), '', $filePath);
        $relativePath = str_replace('\\', '/', $relativePath); // Fix Windows paths
        $fileUrl = $relativePath;

        if ($mimeType === 'application/pdf') {
            // For PDFs, create an embed page
            $html = '<!DOCTYPE html>
<html>
<head>
    <title>View Certificate - ' . $filename . '</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f5f5; }
        embed { width: 100%; height: 100vh; }
    </style>
</head>
<body>
    <embed src="' . $fileUrl . '" type="application/pdf" width="100%" height="100%">
</body>
</html>';
            return response($html, 200, ['Content-Type' => 'text/html']);
        } else {
            // For images and other files, return direct file
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
        }
    }

    public function downloadResaleCertificate($b2bId)
    {
        $b2b = B2b::findOrFail($b2bId);

        if (!$b2b->resale_certificate_path) {
            abort(404, 'No resale certificate found');
        }

        $filePath = null;

        // Check if it's a full path starting with upload/
        if (str_starts_with($b2b->resale_certificate_path, 'upload/')) {
            $filePath = public_path($b2b->resale_certificate_path);
        }
        // Check if it's a Laravel storage path (old method)
        elseif (str_contains($b2b->resale_certificate_path, '/')) {
            $filePath = storage_path('app/' . $b2b->resale_certificate_path);
        }
        // Check if it's just a filename (old method)
        else {
            // Try public directory first (new method)
            $publicPath = public_path('upload/resale_certificates/' . $b2b->resale_certificate_path);
            if (file_exists($publicPath)) {
                $filePath = $publicPath;
            } else {
                // Try storage directory (old method)
                $storagePath = storage_path('app/private/resale_certificates/' . $b2b->resale_certificate_path);
                if (file_exists($storagePath)) {
                    $filePath = $storagePath;
                }
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, 'resale_certificate_' . $b2b->business_name . '_' . $b2b->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }

    public function exportCommissionReport()
    {
        return Excel::download(new CommissionExport, 'commission_report_' . date('Y-m-d') . '.xlsx');
    }

    public function exportBuyReport()
    {
        return Excel::download(new BuyReportExport, 'buy_report_' . date('Y-m-d') . '.xlsx');
    }

    public function approvePayoutRequest(Request $request, $id)
    {
        $payoutRequest = PayoutRequest::findOrFail($id);
        $payoutRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        if ($payoutRequest->commission_id) {
            // Update only the specific commission to 'available' (ready for use)
            Commission::where('id', $payoutRequest->commission_id)
                ->where('reseller_id', $payoutRequest->reseller_id)
                ->update(['status' => 'available']);

            $message = 'Payout request approved and commission marked as available for use';
        } else {
            // Update all pending commissions for this reseller to 'available' (legacy behavior)
            Commission::where('reseller_id', $payoutRequest->reseller_id)
                ->where('status', 'pending')
                ->update(['status' => 'available']);

            $message = 'Payout request approved and all pending commissions marked as available for use';
        }

        return redirect()->back()->with('success', $message);
    }

    public function rejectPayoutRequest(Request $request, $id)
    {
        $payoutRequest = PayoutRequest::findOrFail($id);
        $payoutRequest->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Payout request rejected');
    }

    public function approvePaymentUpdate(Request $request, $id)
    {
        $paymentUpdate = PaymentUpdate::findOrFail($id);
        $paymentUpdate->update(['status' => 'approved']);

        // Update the order payment_status to 'paid' and status to completed (3)
        $paymentUpdate->order->update([
            'payment_status' => 'paid',
            'status' => 3 // Mark as completed
        ]);

        return redirect()->back()->with('success', 'Payment update approved and order marked as paid');
    }
}
