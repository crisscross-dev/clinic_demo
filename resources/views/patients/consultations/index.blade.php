@push('styles')
@vite(['resources/css/patients/consultations/index.css'])
@endpush

@php
// Allow this view to be used from either the index (collection) or show (single)
if (!isset($consultations)) {
$consultations = collect();
}
@endphp

<!-- View Consultation Modal -->
<div class="modal fade" id="gridExampleModal" tabindex="-1" aria-labelledby="gridExampleLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-top modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 id="consultationModalTitle" class="modal-title">
                    Consultation Record
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-2">
                @if($consultations->isEmpty())
                <p class="text-center">No consultations found.</p>
                @else
                <div id="recordContainer">
                    @foreach($consultations as $rec)
                    @php $formattedDate = optional($rec->created_at)->format('F j, Y | g:i A'); @endphp

                    <div data-index="{{ $loop->index }}" @unless($loop->first) hidden @endunless>
                        <div class="header-button">
                            <!-- Left buttons -->
                            <div class="d-flex align-items-center gap-2">
                                <!-- Delete -->
                                <form action="{{ route('patients.consultations.destroy', [$patient, $rec]) }}" method="POST" class="delete-form" data-delete-type="consultation">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-general btn-sm btn-red">Delete</button>
                                </form>

                                <!-- Edit -->
                                <button type="button" class="btn-general btn-sm btn-blue"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editConsultationModal"
                                    data-update-url="{{ route('patients.consultations.update', [$patient, $rec]) }}"
                                    data-chief-complaint="{{ e($rec->chief_complaint) }}"
                                    data-temperature="{{ $rec->temperature }}"
                                    data-blood-pressure="{{ e($rec->blood_pressure) }}"
                                    data-pulse-rate="{{ $rec->pulse_rate }}"
                                    data-respiratory-rate="{{ $rec->respiratory_rate }}"
                                    data-spo2="{{ $rec->spo2 }}"
                                    data-lmp="{{ $rec->lmp ? \Carbon\Carbon::parse($rec->lmp)->format('Y-m-d') : '' }}"
                                    data-pain-scale="{{ e($rec->pain_scale) }}"
                                    data-assessment="{{ e($rec->assessment) }}"
                                    data-intervention="{{ e($rec->intervention) }}"
                                    data-outcome="{{ e($rec->outcome) }}">
                                    Edit
                                </button>

                                <!-- Download -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-gray dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <!-- Icon (always visible) -->
                                        <i class="bi bi-download"></i>
                                        <!-- Text (hidden on small screens) -->
                                        <span class="d-none d-md-inline"> Download</span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item btn-sm" href="{{ route('patients.consultations.download', [$patient, $rec]) }}">Download PDF</a></li>
                                        <li><a class="dropdown-item btn-sm" href="{{ route('patients.consultations.downloadAll', [$patient]) }}">Download All</a></li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Right buttons -->
                            <div class=" right-button">
                                <button type="button" class="btn-general btn-sm btn-blue js-prev" onclick="ConsultationModal.prev()">Prev</button>

                                <span class="counter-box recordCounterWrapper">
                                    <span class="recordCounter">{{ $loop->first ? 1 : $loop->index + 1 }}</span>
                                    <span class="text-muted"> / {{ $consultations->count() }}</span>
                                </span>

                                <button type="button" class="btn-general btn-sm btn-blue js-next" onclick="ConsultationModal.next()">Next</button>
                            </div>
                        </div>



                        <div class="card-body">
                            <div class="row align-items-center mb-3 p-2 bg-light rounded">
                                <!-- Chief Complaint -->
                                <div class="col-lg-8 col-12 mb-2 mb-lg-0">
                                    <div class="info-item">
                                        <span class="label fw-bold text-danger">CHIEF COMPLAINT:</span>
                                        <span class="fw-bold text-dark text-uppercase">
                                            {{ $rec->chief_complaint ?: '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-12 text-lg-end text-start">
                                    <strong>Date:</strong> {{ $formattedDate ?? '—' }}
                                </div>
                            </div>

                            <!-- Assessed by -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="info-item">
                                        <span class="label">Assessed by:</span>
                                        <span class="value">{{ $rec->assessed_by ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Left info -->
                                <div class="col-lg-5 col-12">
                                    <div class="info-item"><span class="label">Temperature:</span> <span class="value">{{ $rec->temperature ? $rec->temperature.' °C' : '-' }}</span></div>
                                    <div class="info-item"><span class="label">Blood Pressure:</span> <span class="value">{{ $rec->blood_pressure ?: '-' }}</span></div>
                                    <div class="info-item"><span class="label">Pulse Rate:</span> <span class="value">{{ $rec->pulse_rate ? $rec->pulse_rate.' bpm' : '-' }}</span></div>
                                    <div class="info-item"><span class="label">Respiratory Rate:</span> <span class="value">{{ $rec->respiratory_rate ? $rec->respiratory_rate.' / min' : '-' }}</span></div>
                                    <div class="info-item"><span class="label">SpO₂:</span> <span class="value">{{ $rec->spo2 ? $rec->spo2.' %' : '-' }}</span></div>
                                    <div class="info-item"><span class="label">LMP:</span> <span class="value">{{ $rec->lmp ? \Carbon\Carbon::parse($rec->lmp)->format('F j, Y') : '-' }}</span></div>
                                    <div class="info-item"><span class="label">Pain Scale:</span> <span class="value">{{ $rec->pain_scale ?: '-' }}</span></div>
                                    <div class="info-item"><span class="label">Medicines:</span> <span class="value">
                                            @php
                                            $dispensedMedicines = $rec->getDispensedMedicinesFromTransactions();
                                            // Group medicines by ID and sum quantities
                                            $groupedMedicines = [];
                                            if($dispensedMedicines && count($dispensedMedicines) > 0) {
                                            foreach($dispensedMedicines as $med) {
                                            $medId = $med['item_id'] ?? 'unknown';
                                            $medName = $med['name'] ?? 'Unknown';
                                            $medQuantity = $med['quantity'] ?? 0;

                                            if(!isset($groupedMedicines[$medId])) {
                                            $groupedMedicines[$medId] = [
                                            'name' => $medName,
                                            'total_quantity' => 0
                                            ];
                                            }

                                            $groupedMedicines[$medId]['total_quantity'] += $medQuantity;
                                            }
                                            }
                                            @endphp
                                            @if(count($groupedMedicines) > 0)
                                            <ul style="margin: 0; padding-left: 20px;">
                                                @foreach($groupedMedicines as $med)
                                                <li>{{ $med['total_quantity'] }} {{ $med['name'] }}</li>
                                                @endforeach
                                            </ul>
                                            @else
                                            -
                                            @endif
                                        </span></div>
                                </div>

                                <!-- Right info -->
                                <div class="col-lg-7 col-12">
                                    <div class="info-item"><span class="label">Assessment:</span> <span class="value">{{ $rec->assessment ?: '-' }}</span></div>
                                    <div class="info-item"><span class="label">Intervention:</span> <span class="value">{{ $rec->intervention ?: '-' }}</span></div>
                                    <div class="info-item"><span class="label">Current Outcome:</span> <span class="value">{{ $rec->outcome ?: 'No outcome set yet.' }}</span></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.js-edit');
        if (!btn) return;
        const href = btn.dataset.href;
        if (!href) {
            console.warn('Edit button clicked but data-href is missing');
            return;
        }
        // Prevent default and navigate explicitly so clicks work even if anchors are blocked
        e.preventDefault();
        window.location.assign(href);
    });

    // Handle edit consultation modal
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editConsultationModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget; // Button that triggered the modal
                const updateUrl = button.getAttribute('data-update-url');
                const form = editModal.querySelector('#editConsultationFormModal');

                console.log('🔧 EDIT MODAL: Setting form action to:', updateUrl);

                if (updateUrl && form) {
                    form.setAttribute('action', updateUrl);
                    console.log('✅ EDIT MODAL: Form action set successfully');
                } else {
                    console.error('❌ EDIT MODAL: Missing updateUrl or form', {
                        updateUrl,
                        form
                    });
                }

                // Set all form field values from data attributes
                const fields = [
                    'chief-complaint', 'temperature', 'blood-pressure', 'pulse-rate',
                    'respiratory-rate', 'spo2', 'lmp', 'pain-scale', 'assessment',
                    'intervention', 'outcome'
                ];

                fields.forEach(field => {
                    const value = button.getAttribute('data-' + field);
                    const input = form.querySelector(`[name="${field.replace('-', '_')}"]`);
                    if (input && value !== null) {
                        input.value = value;
                    }
                });
            });
        }
    });
</script>
@endpush