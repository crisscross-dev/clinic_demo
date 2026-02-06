<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\StockTransaction;
use Mpdf\Mpdf;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::with('category');

        // Optional search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Optional category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $items = $query->orderBy('name')->paginate(50);
        $categories = Category::orderBy('name')->get();

        return view('inventory.index', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'low_stock_reminder' => 'nullable|integer|min:0',
        ]);

        // Initialize stock and derive status (schema has no price/new_delivery)
        $data['total_stock'] = 0;
        $data['low_stock_reminder'] = $data['low_stock_reminder'] ?? 5;
        $data['status'] = 'Out of Stock';

        InventoryItem::create($data);

        return redirect()->route('inventory.index')->with('success', 'Inventory item created.');
    }

    public function update(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'low_stock_reminder' => 'nullable|integer|min:0',
        ]);

        $item->update($data);

        return redirect()->route('inventory.index')->with('success', 'Item updated successfully.');
    }


    public function restock(Request $request, InventoryItem $item)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $item->total_stock = (int) $item->total_stock + (int) $data['amount'];
        $item->status = $item->total_stock <= 0
            ? 'Out of Stock'
            : ($item->total_stock < ($item->low_stock_reminder ?? 5) ? 'Low Stock' : 'In Stock');
        $item->save();

        return redirect()->route('inventory.index')->with('success', 'Item restocked.');
    }

    public function destroy($id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->delete();

        return redirect()->route('inventory.index')->with('success', 'Item deleted successfully.');
    }

    /**
     * Generate inventory usage report as PDF with timeframe filter.
     * Inputs (query): start, end (YYYY-MM-DD). If missing, defaults to current month.
     * Output: PDF listing item name, total deducted in range, and remaining stock now.
     */
    public function reportPdf(Request $request)
    {
        // Parse dates
        $start = $request->query('start');
        $end   = $request->query('end');

        try {
            $startDate = $start ? \Carbon\Carbon::parse($start)->startOfDay() : \Carbon\Carbon::now()->startOfMonth();
            $endDate   = $end   ? \Carbon\Carbon::parse($end)->endOfDay()   : \Carbon\Carbon::now()->endOfMonth();
        } catch (\Exception $e) {
            return back()->with('error', 'Invalid date range.');
        }

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // Checklist flags (default to ALL true if none provided)
        $anyIncludeParam = $request->hasAny([
            'include_used',
            'include_chart',
            'include_unused',
            'include_out',
            'include_low',
            'include_lost_expired', // 👈 NEW flag
            'include_restock', // 👈 RESTOCK flag
            'include_transaction_log', // 👈 TRANSACTION LOG flag
        ]);

        $includeUsed        = $anyIncludeParam ? $request->has('include_used')        : true;
        $includeChart       = $anyIncludeParam ? $request->has('include_chart')       : true;
        $includeUnused      = $anyIncludeParam ? $request->has('include_unused')      : true;
        $includeOut         = $anyIncludeParam ? $request->has('include_out')         : true;
        $includeLow         = $anyIncludeParam ? $request->has('include_low')         : true;
        $includeLostExpired = $anyIncludeParam ? $request->has('include_lost_expired') : true; // 👈 NEW
        $includeRestock     = $anyIncludeParam ? $request->has('include_restock')     : true; // 👈 RESTOCK
        $includeTransactionLog = $anyIncludeParam ? $request->has('include_transaction_log') : true; // 👈 TRANSACTION LOG

        // Aggregate deductions (used + dispensed + lost + expired)
        $deductions = StockTransaction::query()
            ->join('inventory_items', 'stock_transactions.item_id', '=', 'inventory_items.id')
            ->whereIn('stock_transactions.type', ['deduct', 'dispensed', 'expired', 'lost'])
            ->whereBetween('stock_transactions.created_at', [$startDate, $endDate])
            ->groupBy('inventory_items.id', 'inventory_items.name')
            ->select([
                'inventory_items.id as item_id',
                'inventory_items.name as name',
                DB::raw('SUM(stock_transactions.quantity) as total_deducted'),
            ])
            ->orderBy('inventory_items.name')
            ->get()
            ->keyBy('item_id');

        // Current remaining stock
        $items = InventoryItem::query()
            ->select('id', 'name', 'total_stock', 'low_stock_reminder')
            ->orderBy('name')
            ->get();

        $rows = $items->map(function ($item) use ($deductions) {
            $ded = (int) ($deductions[$item->id]->total_deducted ?? 0);
            return [
                'name'           => $item->name,
                'total_deducted' => $ded,
                'remaining'      => (int) $item->total_stock,
            ];
        });

        $usedRows = $rows->filter(fn($r) => $r['total_deducted'] > 0)
            ->sortByDesc('total_deducted')->values();

        $unusedRows = $rows->filter(fn($r) => $r['total_deducted'] === 0)
            ->sortBy('name')->values();

        $outOfStockRows = $items->filter(fn($i) => (int) $i->total_stock <= 0)
            ->map(fn($i) => [
                'name' => $i->name,
                'remaining' => (int) $i->total_stock,
            ])->sortBy('name')->values();

        $lowStockRows = $items->filter(function ($i) {
            $threshold = (int) ($i->low_stock_reminder ?? 5);
            $remaining = (int) $i->total_stock;
            return $remaining > 0 && $remaining < $threshold;
        })->map(fn($i) => [
            'name' => $i->name,
            'remaining' => (int) $i->total_stock,
        ])->sortBy('remaining')->values();

        // 👇 NEW lost/expired breakdown
        $lostExpired = StockTransaction::query()
            ->join('inventory_items', 'stock_transactions.item_id', '=', 'inventory_items.id')
            ->whereIn('stock_transactions.type', ['lost', 'expired'])
            ->whereBetween('stock_transactions.created_at', [$startDate, $endDate])
            ->groupBy('inventory_items.id', 'inventory_items.name')
            ->select([
                'inventory_items.id as item_id',
                'inventory_items.name as name',
                DB::raw('SUM(CASE WHEN stock_transactions.type = "lost" THEN stock_transactions.quantity ELSE 0 END) as total_lost'),
                DB::raw('SUM(CASE WHEN stock_transactions.type = "expired" THEN stock_transactions.quantity ELSE 0 END) as total_expired'),
            ])
            ->get();

        $lostExpiredRows = $lostExpired->map(function ($r) {
            $lost = (int) $r->total_lost;
            $expired = (int) $r->total_expired;
            return [
                'name'    => $r->name,
                'lost'    => $lost,
                'expired' => $expired,
                'total'   => $lost + $expired,
            ];
        })->sortByDesc('total')->values();

        // 👇 NEW restock breakdown
        $restocks = StockTransaction::query()
            ->join('inventory_items', 'stock_transactions.item_id', '=', 'inventory_items.id')
            ->where('stock_transactions.type', 'restock')
            ->whereBetween('stock_transactions.created_at', [$startDate, $endDate])
            ->groupBy('inventory_items.id', 'inventory_items.name')
            ->select([
                'inventory_items.id as item_id',
                'inventory_items.name as name',
                DB::raw('SUM(stock_transactions.quantity) as total_restocked'),
            ])
            ->get();

        $restockRows = $restocks->map(function ($r) use ($items) {
            $item = $items->firstWhere('id', $r->item_id);
            return [
                'name'            => $r->name,
                'total_restocked' => (int) $r->total_restocked,
                'remaining'       => $item ? (int) $item->total_stock : 0,
            ];
        })->sortByDesc('total_restocked')->values();

        // Get detailed restock transactions with date and admin info (only if transaction log is enabled)
        $restockTransactions = collect([]);
        $deductTransactions = collect([]);

        if ($includeTransactionLog) {
            $restockTransactions = StockTransaction::with(['item', 'admin'])
                ->where('type', 'restock')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get detailed deduct/dispensed/lost/expired transactions with date and admin info
            $deductTransactions = StockTransaction::with(['item', 'admin'])
                ->whereIn('type', ['deduct', 'dispensed', 'lost', 'expired'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $rangeLabel = $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');

        // Logo URL for mPDF - use base64 encoding (most reliable)
        $logoPath = public_path('images/logo2_pdf.png');
        $logoUrl = '';

        if (file_exists($logoPath)) {
            try {
                $logoContent = file_get_contents($logoPath);
                if ($logoContent !== false) {
                    $logoBase64 = base64_encode($logoContent);
                    $logoUrl = 'data:image/png;base64,' . $logoBase64;
                }
            } catch (\Exception $e) {
                // Logo loading failed, will show fallback in template
                $logoUrl = '';
            }
        }

        // Generate HTML from view
        $html = view('pdf.inventory_report', [
            'usedRows'         => $usedRows,
            'unusedRows'       => $unusedRows,
            'outOfStockRows'   => $outOfStockRows,
            'lowStockRows'     => $lowStockRows,
            'lostExpiredRows'  => $lostExpiredRows,   // 👈 NEW
            'restockRows'      => $restockRows,       // 👈 RESTOCK
            'restockTransactions' => $restockTransactions, // 👈 Detailed transactions
            'deductTransactions'  => $deductTransactions,  // 👈 Detailed transactions
            'rangeLabel'       => $rangeLabel,
            'generatedAt'      => now()->format('M d, Y h:i A'),
            'logoUrl'          => $logoUrl,

            // flags
            'includeUsed'        => $includeUsed,
            'includeChart'       => $includeChart,
            'includeUnused'      => $includeUnused,
            'includeOut'         => $includeOut,
            'includeLow'         => $includeLow,
            'includeLostExpired' => $includeLostExpired, // 👈 NEW
            'includeRestock'     => $includeRestock,     // 👈 RESTOCK
            'includeTransactionLog' => $includeTransactionLog, // 👈 TRANSACTION LOG
        ])->render();

        // Create mPDF instance with better CSS support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'tempDir' => sys_get_temp_dir(),
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans'
        ]);

        // Write HTML content
        $mpdf->WriteHTML($html);

        $fileName = 'inventory-report-' . now()->format('Ymd_His') . '.pdf';

        // Download the PDF
        return response($mpdf->Output($fileName, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
