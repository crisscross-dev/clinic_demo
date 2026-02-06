<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientInfo;
use App\Models\Consultation;
use App\Models\Admin;
use App\Models\StockTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $totalPatients = PatientInfo::where('status', 'approved')->count();
        $todayRegistrations = PatientInfo::whereDate('created_at', Carbon::today())->count();
        // Count only pending patients with departments (matching pending index filter)
        $totalPendingEmails = PatientInfo::where('status', 'pending')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->count();
        // Replace week/month stats with consultation counts
        $todayConsultations = Consultation::whereDate('created_at', Carbon::today())->count();
        $thisWeekRegistrations = Consultation::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        $thisMonthRegistrations = Consultation::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Get recent consultations (last 5)
        $recentConsultations = Consultation::with('patient')
            ->latest()
            ->limit(5)
            ->get();

        // Top departments by consultations for the current month
        $departmentStats = Consultation::query()
            ->join('patient_infos', 'consultations.patient_id', '=', 'patient_infos.id')
            ->whereMonth('consultations.created_at', Carbon::now()->month)
            ->whereYear('consultations.created_at', Carbon::now()->year)
            ->selectRaw('COALESCE(patient_infos.department, "Not Specified") as department, COUNT(consultations.id) as count')
            ->groupBy('patient_infos.department')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top admins removed

        // Get patient distribution by year level
        $yearLevelStats = PatientInfo::selectRaw('year_level, COUNT(*) as count')
            ->groupBy('year_level')
            ->orderBy('count', 'desc')
            ->get();

        // Consultations timeframes for chart
        $consultations7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $consultations7Days[$day->format('M d')] = Consultation::whereDate('created_at', $day)->count();
        }

        $consultations30Days = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $consultations30Days[$day->format('M d')] = Consultation::whereDate('created_at', $day)->count();
        }

        $consultations12Months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->startOfMonth()->subMonths($i);
            $consultations12Months[$month->format('M Y')] = Consultation::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        // Inventory usage (deduct) per item for today, this week, this month
        $inventoryUsage = collect();
        $inventoryRestocks = collect();
        if (Schema::hasTable('stock_transactions')) {
            $today = Carbon::today()->toDateString();
            // For usage ranges we want rolling windows: last 7 days and last 30 days (inclusive)
            $weekStart = Carbon::today()->copy()->subDays(6)->startOfDay();
            $weekEnd = Carbon::today()->endOfDay();
            $monthStart = Carbon::today()->copy()->subDays(29)->startOfDay();
            $monthEnd = Carbon::today()->endOfDay();

            $inventoryUsage = StockTransaction::query()
                ->join('inventory_items', 'stock_transactions.item_id', '=', 'inventory_items.id')
                ->whereIn('stock_transactions.type', ['deduct', 'dispensed'])
                ->selectRaw(
                    'inventory_items.id as item_id, inventory_items.name, inventory_items.total_stock, inventory_items.low_stock_reminder, '
                        . 'SUM(CASE WHEN DATE(stock_transactions.created_at) = ? THEN stock_transactions.quantity ELSE 0 END) as today_used, '
                        . 'SUM(CASE WHEN stock_transactions.created_at BETWEEN ? AND ? THEN stock_transactions.quantity ELSE 0 END) as week_used, '
                        . 'SUM(CASE WHEN stock_transactions.created_at BETWEEN ? AND ? THEN stock_transactions.quantity ELSE 0 END) as month_used',
                    [$today, $weekStart, $weekEnd, $monthStart, $monthEnd]
                )
                ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.total_stock', 'inventory_items.low_stock_reminder')
                ->orderByDesc('month_used')
                ->limit(30)
                ->get();

            // Inventory restocks per item for today, this week, this month
            $inventoryRestocks = StockTransaction::query()
                ->join('inventory_items', 'stock_transactions.item_id', '=', 'inventory_items.id')
                ->where('stock_transactions.type', 'restock')
                ->selectRaw(
                    'inventory_items.id as item_id, inventory_items.name, inventory_items.total_stock, '
                        . 'SUM(CASE WHEN DATE(stock_transactions.created_at) = ? THEN stock_transactions.quantity ELSE 0 END) as today_restocked, '
                        . 'SUM(CASE WHEN stock_transactions.created_at BETWEEN ? AND ? THEN stock_transactions.quantity ELSE 0 END) as week_restocked, '
                        . 'SUM(CASE WHEN stock_transactions.created_at BETWEEN ? AND ? THEN stock_transactions.quantity ELSE 0 END) as month_restocked',
                    [$today, $weekStart, $weekEnd, $monthStart, $monthEnd]
                )
                ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.total_stock')
                ->orderByDesc('month_restocked')
                ->limit(30)
                ->get();
        }

        // Get admin with null safety
        $admin = Admin::find(session('admin_id'));

        // If admin is not found, redirect to login
        if (!$admin) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        return view('admin.dashboard', compact(
            'totalPatients',
            'todayRegistrations',
            'totalPendingEmails',
            'todayConsultations',
            'thisWeekRegistrations',
            'thisMonthRegistrations',
            'recentConsultations',
            'departmentStats',
            'yearLevelStats',
            'consultations7Days',
            'consultations30Days',
            'consultations12Months',
            'inventoryUsage',
            'inventoryRestocks',
            'admin'
        ));
    }

    public function consultationsSeries(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $start = Carbon::parse($request->query('start'))->startOfDay();
        $end = Carbon::parse($request->query('end'))->endOfDay();
        $days = $start->diffInDays($end);
        // Optional admin filter
        $adminId = $request->query('admin_id');
        $hasAdminColumn = Schema::hasColumn('consultations', 'admin_id');

        // If range > 90 days, aggregate by month; else aggregate by day
        if ($days > 90) {
            // Monthly aggregation
            $cursor = $start->copy()->startOfMonth();
            $series = [];
            while ($cursor <= $end) {
                $label = $cursor->format('M Y');
                $query = Consultation::query();
                if ($hasAdminColumn && $adminId) {
                    $query->where('admin_id', $adminId);
                }
                $series[$label] = $query->whereYear('created_at', $cursor->year)
                    ->whereMonth('created_at', $cursor->month)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $cursor->addMonthNoOverflow();
            }
        } else {
            // Daily aggregation
            $cursor = $start->copy();
            $series = [];
            while ($cursor <= $end) {
                $label = $cursor->format('M d');
                $query = Consultation::query();
                if ($hasAdminColumn && $adminId) {
                    $query->where('admin_id', $adminId);
                }
                $series[$label] = $query->whereDate('created_at', $cursor)->count();
                $cursor->addDay();
            }
        }

        return response()->json($series);
    }

    public function inventorySeries(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        if (!Schema::hasTable('stock_transactions') || !Schema::hasTable('inventory_items')) {
            return response()->json(['labels' => [], 'used' => [], 'restock' => [], 'rangeLabel' => '']);
        }

        $start = Carbon::parse($request->query('start'))->startOfDay();
        $end = Carbon::parse($request->query('end'))->endOfDay();

        // Aggregate used (deduct + dispensed) per item
        $usedRows = StockTransaction::query()
            ->join('inventory_items', 'stock_transactions.item_id', '=', 'inventory_items.id')
            ->whereIn('stock_transactions.type', ['deduct', 'dispensed'])
            ->whereBetween('stock_transactions.created_at', [$start, $end])
            ->groupBy('inventory_items.id', 'inventory_items.name')
            ->select('inventory_items.id as item_id', 'inventory_items.name as name', DB::raw('SUM(stock_transactions.quantity) as qty'))
            ->get();

        // Aggregate restock per item
        $restockRows = StockTransaction::query()
            ->join('inventory_items', 'stock_transactions.item_id', '=', 'inventory_items.id')
            ->where('stock_transactions.type', 'restock')
            ->whereBetween('stock_transactions.created_at', [$start, $end])
            ->groupBy('inventory_items.id', 'inventory_items.name')
            ->select('inventory_items.id as item_id', 'inventory_items.name as name', DB::raw('SUM(stock_transactions.quantity) as qty'))
            ->get();

        // Merge into a single map keyed by item_id
        $map = [];
        foreach ($usedRows as $row) {
            $map[$row->item_id] = [
                'name' => $row->name,
                'used' => (int) $row->qty,
                'restock' => 0,
            ];
        }
        foreach ($restockRows as $row) {
            if (!isset($map[$row->item_id])) {
                $map[$row->item_id] = [
                    'name' => $row->name,
                    'used' => 0,
                    'restock' => (int) $row->qty,
                ];
            } else {
                $map[$row->item_id]['restock'] = (int) $row->qty;
            }
        }

        // Sort by used desc, then name
        uasort($map, function ($a, $b) {
            if ($a['used'] === $b['used']) return strcmp($a['name'], $b['name']);
            return $b['used'] <=> $a['used'];
        });

        $labels = array_map(fn($x) => $x['name'], $map);
        $used = array_map(fn($x) => (int) $x['used'], $map);
        $restock = array_map(fn($x) => (int) $x['restock'], $map);

        $rangeLabel = $start->format('M d, Y') . ' — ' . $end->format('M d, Y');

        return response()->json([
            'labels' => array_values($labels),
            'used' => array_values($used),
            'restock' => array_values($restock),
            'rangeLabel' => $rangeLabel,
        ]);
    }

    public function downloadConsultationsPdf(Request $request)
    {
        // Get query parameters for filtering
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $department = $request->get('department');
        $course = $request->get('course');
        $adminId = $request->get('admin_id'); // Filter by specific admin

        // Build the query with admin relationship
        $query = Consultation::with([
            'patient' => function ($q) {
                $q->select('id', 'first_name', 'last_name', 'department', 'course', 'year_level');
            },
            'admin' => function ($q) {
                $q->select('id', 'firstname', 'lastname', 'middlename', 'prefix');
            }
        ]);

        // Apply filters
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($department) {
            $query->whereHas('patient', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }
        if ($course) {
            $query->whereHas('patient', function ($q) use ($course) {
                $q->where('course', 'like', '%' . $course . '%');
            });
        }
        // Filter by admin ID if provided (for personal consultations)
        if ($adminId) {
            $query->where('admin_id', $adminId);
        }

        // Get consultations and group by student
        $consultationsData = $query->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('patient_id')
            ->map(function ($consultations, $patientId) {
                $firstConsultation = $consultations->first();
                $patient = $firstConsultation->patient;

                return [
                    'student_name' => $patient ?
                        $patient->last_name . ', ' . $patient->first_name :
                        'Unknown Student',
                    'course' => $patient ?
                        ($patient->year_level ?? '') . ' ' . ($patient->course ?? '') :
                        '—',
                    'consultations' => $consultations->map(function ($consultation) {
                        $adminName = '—';
                        if ($consultation->admin) {
                            $adminName = trim(($consultation->admin->prefix ?? '') . ' ' .
                                ($consultation->admin->lastname ?? '') . ', ' .
                                ($consultation->admin->firstname ?? ''));
                        }

                        return [
                            'id' => $consultation->id, // Add consultation ID for medicine lookup
                            'date' => $consultation->created_at ? $consultation->created_at->format('M j, Y') : '—',
                            'chief_complaint' => $consultation->chief_complaint ?? '—',
                            'medicine_given' => $consultation->dispensed_medicines_list ?: '—',
                            'outcome' => $consultation->outcome ?? '—',
                            'admin_name' => $adminName,
                        ];
                    })->toArray()
                ];
            })
            ->values()
            ->toArray();

        // Build filter info
        $filterInfo = [];
        if ($dateFrom || $dateTo) {
            $dateRange = '';
            if ($dateFrom) $dateRange .= 'From: ' . \Carbon\Carbon::parse($dateFrom)->format('M j, Y');
            if ($dateTo) $dateRange .= ($dateRange ? ' | ' : '') . 'To: ' . \Carbon\Carbon::parse($dateTo)->format('M j, Y');
            $filterInfo['dateRange'] = $dateRange;
        }
        if ($department) $filterInfo['department'] = $department;
        if ($course) $filterInfo['course'] = $course;

        // Logo setup for mPDF - use base64 encoding (most reliable)
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

        // Count unique students
        $uniqueStudentsCount = collect($consultationsData)->unique('student_name')->count();

        // Collect unique admin names from all consultations
        $adminNames = collect($consultationsData)
            ->flatMap(function ($group) {
                return collect($group['consultations'])->pluck('admin_name');
            })
            ->filter(function ($name) {
                return $name && $name !== '—';
            })
            ->unique()
            ->values()
            ->toArray();

        // Create a summary of who conducted the consultations
        $conductedBy = 'N/A';
        $conductedByList = [];
        if ($adminId) {
            // Personal PDF - show the specific admin's name
            $admin = Admin::find($adminId);
            $conductedBy = $admin ? $admin->full_name : 'Unknown Admin';
            $conductedByList = [$conductedBy];
        } else {
            // General PDF - show all admins who conducted consultations
            if (count($adminNames) === 0) {
                $conductedBy = 'N/A';
                $conductedByList = ['N/A'];
            } else {
                $conductedByList = $adminNames;
                $conductedBy = implode(', ', $adminNames); // Keep for backward compatibility
            }
        }

        // Determine admin model to pass into the view (prefer session admin_id, fallback to Auth)
        $admin = null;
        if (session('admin_id')) {
            $admin = Admin::find(session('admin_id'));
        } elseif (Auth::check()) {
            $admin = Auth::user();
        }

        // Generate HTML from view
        $html = view('pdf/consultations_list_pdf', array_merge([
            'consultations' => $consultationsData,
            'logoUrl' => $logoUrl,
            'uniqueStudentsCount' => $uniqueStudentsCount,
            'admin' => $admin,
            'conductedBy' => $conductedBy,
            'conductedByList' => $conductedByList,
        ], $filterInfo))->render();

        // Create mPDF instance
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'tempDir' => sys_get_temp_dir(),
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans'
        ]);

        // Write HTML content
        $mpdf->WriteHTML($html);

        $fileName = 'Consultations_Report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        // Download the PDF
        return response($mpdf->Output($fileName, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
