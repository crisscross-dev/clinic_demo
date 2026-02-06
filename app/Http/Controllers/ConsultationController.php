<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\PatientInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class ConsultationController extends Controller
{
    /**
     * List consultations for a patient.
     */
    public function index(PatientInfo $patient, Consultation $consultation)
    {
        $this->authorizeConsultation($patient, $consultation);
        return view('patients.consultations.edit', compact('patient', 'consultation'));
    }

    /**
     * Show form to create a new consultation for a patient.
     */
    public function create(PatientInfo $patient)
    {
        $consultation = new Consultation();

        // Get all inventory items for medicine search
        $inventoryItems = \App\Models\InventoryItem::with('category')
            ->orderBy('name')
            ->get();

        // Prepare clean medicine data for JavaScript (no linting issues)
        $medicinesForJs = $inventoryItems->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'stock' => $item->total_stock
            ];
        });

        return view('patients.consultations.create', compact('patient', 'consultation', 'inventoryItems', 'medicinesForJs'));
    }

    /**
     * Store a new consultation.
     */
    public function store(Request $request, PatientInfo $patient)
    {
        Log::info('🚀 === CONSULTATION STORE METHOD CALLED ===', [
            'request_method' => $request->method(),
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'has_medicines' => $request->has('medicines'),
            'medicines_input_exists' => $request->input('medicines') !== null,
            'medicines_is_array' => is_array($request->input('medicines')),
            'medicines_count' => is_array($request->input('medicines')) ? count($request->input('medicines')) : 0,
            'medicines_raw' => $request->input('medicines'),
            'all_input_keys' => array_keys($request->all())
        ]);

        // Additional debugging for medicine-related form fields
        $medicineFields = [];
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'medicine') !== false) {
                $medicineFields[$key] = $value;
            }
        }

        if (!empty($medicineFields)) {
            Log::info('🏥 Medicine-related form fields found:', $medicineFields);
        } else {
            Log::warning('⚠️ No medicine-related form fields found in request');
        }

        $data = $this->validateData($request);

        if ($adminId = (int) session('admin_id')) {
            $data['admin_id'] = $adminId;

            // fetch the admin record
            $admin = \App\Models\Admin::find($adminId);
            if ($admin) {
                $data['assessed_by'] = $admin->full_name; // accessor from your Admin model
            }
        }

        // Create via relation to set patient_id automatically
        $consultation = $patient->consultations()->create($data);

        // Handle medicine dispensing
        if ($request->has('medicines') && is_array($request->medicines)) {
            try {
                Log::info('Medicine dispensing data received', [
                    'medicines' => $request->medicines,
                    'consultation_id' => $consultation->id
                ]);
                $this->processMedicineDispensing($request->medicines, $consultation);
                Log::info('Medicine dispensing completed successfully');
            } catch (\Exception $e) {
                Log::error('Medicine dispensing failed', [
                    'error' => $e->getMessage(),
                    'medicines' => $request->medicines,
                    'consultation_id' => $consultation->id
                ]);
                // Don't throw the exception to avoid breaking the consultation creation
                // Just log it for debugging
            }
        } else {
            Log::info('No medicines to dispense', [
                'has_medicines' => $request->has('medicines'),
                'medicines_value' => $request->input('medicines'),
                'all_inputs' => array_keys($request->all())
            ]);
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Consultation created successfully!');
    }

    /**
     * Process medicine dispensing and create stock transactions
     */
    private function processMedicineDispensing($medicines, $consultation)
    {
        Log::info('Processing medicine dispensing', [
            'medicines_count' => count($medicines),
            'consultation_id' => $consultation->id,
            'medicines_data' => $medicines
        ]);

        $dispensedMedicines = [];

        foreach ($medicines as $index => $medicine) {
            Log::info("Processing medicine {$index}", [
                'raw_medicine_data' => $medicine,
                'item_id_empty' => empty($medicine['item_id']),
                'quantity_empty' => empty($medicine['quantity']),
                'item_id_value' => $medicine['item_id'] ?? 'not_set',
                'quantity_value' => $medicine['quantity'] ?? 'not_set',
                'medicine_keys' => array_keys($medicine ?? [])
            ]);

            if (empty($medicine['item_id']) || empty($medicine['quantity'])) {
                Log::warning('Skipping empty medicine entry', [
                    'medicine' => $medicine,
                    'item_id_check' => empty($medicine['item_id']),
                    'quantity_check' => empty($medicine['quantity'])
                ]);
                continue; // Skip empty entries
            }

            $inventoryItem = \App\Models\InventoryItem::find($medicine['item_id']);

            if (!$inventoryItem) {
                Log::error('Inventory item not found', ['item_id' => $medicine['item_id']]);
                continue; // Skip if item not found
            }

            $quantity = (int) $medicine['quantity'];
            Log::info('Processing valid medicine', [
                'item_name' => $inventoryItem->name,
                'requested_quantity' => $quantity,
                'current_stock' => $inventoryItem->total_stock
            ]);

            if ($quantity > $inventoryItem->total_stock) {
                Log::error('Insufficient stock', [
                    'item_name' => $inventoryItem->name,
                    'available' => $inventoryItem->total_stock,
                    'requested' => $quantity
                ]);
                throw new \Exception("Insufficient stock for {$inventoryItem->name}. Available: {$inventoryItem->total_stock}, Requested: {$quantity}");
            }

            // Add to dispensed medicines array for consultation record
            $dispensedMedicines[] = [
                'name' => $inventoryItem->name,
                'quantity' => $quantity,
                'instructions' => $medicine['instructions'] ?? '' // Empty if not provided
            ];

            // Deduct from inventory
            $oldStock = $inventoryItem->total_stock;
            $inventoryItem->decrement('total_stock', $quantity);
            $newStock = $inventoryItem->fresh()->total_stock;

            Log::info('Stock decremented', [
                'item_name' => $inventoryItem->name,
                'old_stock' => $oldStock,
                'decremented_by' => $quantity,
                'new_stock' => $newStock
            ]);

            // Update inventory status based on stock level
            if ($inventoryItem->total_stock <= 0) {
                $inventoryItem->status = 'Out of Stock';
            } elseif ($inventoryItem->total_stock < ($inventoryItem->low_stock_reminder ?? 5)) {
                $inventoryItem->status = 'Low Stock';
            } else {
                $inventoryItem->status = 'In Stock';
            }
            $inventoryItem->save();

            // Create stock transaction record
            $transactionData = [
                'item_id' => $inventoryItem->id,
                'type' => 'dispensed',
                'quantity' => $quantity,
                'admin_id' => session('admin_id'),
                'consultation_id' => $consultation->id,
                'notes' => "Dispensed to patient in consultation #{$consultation->id}"
            ];

            Log::info('Creating stock transaction', $transactionData);

            $transaction = \App\Models\StockTransaction::create($transactionData);

            Log::info('Stock transaction created successfully', [
                'transaction_id' => $transaction->id,
                'item_name' => $inventoryItem->name,
                'quantity' => $quantity,
                'consultation_id' => $transaction->consultation_id
            ]);
        }

        // Save dispensed medicines to consultation record
        if (!empty($dispensedMedicines)) {
            $consultation->update(['dispensed_medicines' => $dispensedMedicines]);
            Log::info('Dispensed medicines saved to consultation', [
                'consultation_id' => $consultation->id,
                'dispensed_medicines' => $dispensedMedicines
            ]);
        }
    }

    /**
     * Show a specific consultation.
     */
    public function show(PatientInfo $patient, Consultation $consultation)
    {
        // Avoid rendering a partial as a full page; send back to patient view
        return redirect()->route('patients.show', $patient);
    }

    /**
     * Edit a consultation.
     */
    public function edit(PatientInfo $patient, Consultation $consultation)
    {
        $this->authorizeConsultation($patient, $consultation);

        // Get all inventory items for medicine search in edit modal
        $inventoryItems = \App\Models\InventoryItem::with('category')
            ->orderBy('name')
            ->get();

        // Prepare clean medicine data for JavaScript (no linting issues)
        $medicinesForJs = $inventoryItems->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'stock' => $item->total_stock
            ];
        });

        return view('patients.consultations.edit', compact('patient', 'consultation', 'medicinesForJs'));
    }

    /**
     * Update a consultation.
     */
    public function update(Request $request, PatientInfo $patient, Consultation $consultation)
    {
        $this->authorizeConsultation($patient, $consultation);

        // Simple debug - write to a plain text file
        $debugInfo = [
            'timestamp' => now()->toDateTimeString(),
            'consultation_id' => $consultation->id,
            'has_medicines' => $request->has('medicines'),
            'medicines_data' => $request->medicines ?? 'NONE',
            'all_request_data' => $request->all()
        ];
        file_put_contents(storage_path('logs/edit_debug.txt'), print_r($debugInfo, true) . "\n\n", FILE_APPEND);

        // Debug logging for update method
        Log::info('ConsultationController UPDATE method called', [
            'consultation_id' => $consultation->id,
            'patient_id' => $patient->id,
            'request_has_medicines' => $request->has('medicines'),
            'medicines_is_array' => $request->has('medicines') ? is_array($request->medicines) : false,
            'medicines_count' => $request->has('medicines') && is_array($request->medicines) ? count($request->medicines) : 0,
            'all_request_keys' => array_keys($request->all())
        ]);

        // Log medicines data if present
        if ($request->has('medicines')) {
            Log::info('UPDATE: Medicines data received', [
                'medicines_data' => $request->medicines,
                'medicines_type' => gettype($request->medicines)
            ]);
        } else {
            Log::warning('UPDATE: No medicines key found in request');
        }

        // Update consultation data first
        $consultation->update($this->validateData($request));

        // Handle new medicines being dispensed
        if ($request->has('medicines') && is_array($request->medicines)) {
            Log::info('UPDATE: Calling processMedicineDispensing');
            $this->processMedicineDispensing($request->medicines, $consultation);
        } else {
            Log::info('UPDATE: Skipping medicine processing', [
                'has_medicines' => $request->has('medicines'),
                'is_array' => $request->has('medicines') ? is_array($request->medicines) : false
            ]);
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Consultation updated successfully!');
    }

    /**
     * Delete a consultation.
     */
    public function destroy(PatientInfo $patient, Consultation $consultation)
    {
        $this->authorizeConsultation($patient, $consultation);
        $consultation->delete();
        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Consultation deleted successfully!');
    }

    /**
     * Download a consultation as PDF using Snappy (wkhtmltopdf).
     */
    public function downloadPdf(PatientInfo $patient, Consultation $consultation)
    {
        $this->authorizeConsultation($patient, $consultation);

        $fallback = fn($value) => empty($value) ? '—' : $value;
        // Patient data for display
        $patientData = [
            'last_name' => $fallback($patient->last_name),
            'first_name' => $fallback($patient->first_name),
            'year_level' => $patient->year_level ?? '',
            'course' => $patient->course ?? '—',
            'contact_no' => $fallback($patient->contact_no),
            'sex' => $fallback($patient->sex),
            'address' => $fallback($patient->address),
            'age' => $fallback($patient->age),
        ];

        // Formatted dates
        $formatted = [
            'createdStr' => $consultation->created_at ? \Carbon\Carbon::parse($consultation->created_at)->format('F j, Y - g:i A') : '—',
            'lmpStr' => $consultation->lmp ? \Carbon\Carbon::parse($consultation->lmp)->format('F j, Y') : '—',
        ];

        $val = function ($src, $key, $suffix = '') {
            $v = data_get($src, $key);
            return ($v !== null && $v !== '') ? ($v . $suffix) : '—';
        };

        // Logo URL for DomPDF - use base64 encoding (most reliable)
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
        $html = view('pdf/consultation_pdf', [
            'patient' => $patient,
            'consultation' => $consultation,
            'patientData' => $patientData,
            'formatted' => $formatted,
            'val' => $val,
            'logoUrl' => $logoUrl,
        ])->render();

        // Create mPDF instance
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

        // Make surname safe for filenames
        $safeSurname = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patient->last_name);

        // Download the PDF
        return response($mpdf->Output($safeSurname . '_consultation.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $safeSurname . '_consultation.pdf"');
    }

    /**
     * Download all consultations for a patient as a single PDF.
     */
    public function downloadAll(PatientInfo $patient)
    {
        $fallback = fn($value) => empty($value) ? '—' : $value;

        // Patient data
        $patientData = [
            'last_name'  => $fallback($patient->last_name),
            'first_name' => $fallback($patient->first_name),
            'year_level' => $patient->year_level ?? '',
            'course'     => $patient->course ?? '—',
            'contact_no' => $fallback($patient->contact_no),
            'sex'        => $fallback($patient->sex),
            'address'    => $fallback($patient->address),
            'age'        => $fallback($patient->age),
        ];

        // Consultations
        $consultationsData = $patient->consultations()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($c) use ($fallback) {
                return [
                    'consultation_obj' => $c, // Include the consultation object for medicine access
                    'created_at'       => $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('F j, Y | g:i A') : '—',
                    'chief_complaint'  => $fallback($c->chief_complaint),
                    'temperature'      => $c->temperature !== null ? $c->temperature . ' °C' : '—',
                    'blood_pressure'   => $fallback($c->blood_pressure),
                    'pulse_rate'       => $c->pulse_rate !== null ? $c->pulse_rate . ' bpm' : '—',
                    'respiratory_rate' => $c->respiratory_rate !== null ? $c->respiratory_rate . ' / min' : '—',
                    'spo2'             => $c->spo2 !== null ? $c->spo2 . ' %' : '—',
                    'pain_scale'       => $fallback($c->pain_scale),
                    'lmp'              => $c->lmp ? \Carbon\Carbon::parse($c->lmp)->format('F j, Y') : '—',
                    'assessment'       => $fallback($c->assessment),
                    'intervention'     => $fallback($c->intervention),
                    'medicine_given'   => $c->dispensed_medicines_list ?: '—',
                    'outcome'          => $fallback($c->outcome),
                    'assessed_by'      => $fallback($c->assessed_by),
                ];
            });

        // Logo URL for DomPDF - use base64 encoding (most reliable)
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
        $html = view('pdf/consultation_pdf_all', [
            'patientData'    => $patientData,
            'consultations'  => $consultationsData,
            'logoUrl'        => $logoUrl,
        ])->render();

        // Create mPDF instance
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

        $safeSurname = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patient->last_name);

        // Download the PDF
        return response($mpdf->Output($safeSurname . '_consultations_all.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $safeSurname . '_consultations_all.pdf"');
    }


    private function validateData(Request $request): array
    {
        return $request->validate([
            'chief_complaint' => ['nullable', 'string', 'max:255'],
            'temperature' => ['nullable', 'numeric'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'pulse_rate' => ['nullable', 'integer'],
            'respiratory_rate' => ['nullable', 'integer'],
            'spo2' => ['nullable', 'integer'],
            'lmp' => ['nullable', 'date'],
            'pain_scale' => ['nullable', 'string', 'max:50'],
            'assessment' => ['nullable', 'string'],
            'intervention' => ['nullable', 'string'],
            'outcome' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function authorizeConsultation(PatientInfo $patient, Consultation $consultation): void
    {
        if ($consultation->patient_id !== $patient->id) {
            abort(404);
        }
    }
}
