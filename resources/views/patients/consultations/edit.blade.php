<!-- Edit Consultation Modal (Bootstrap 5 Only) -->
<div class="modal fade" id="editConsultationModal" tabindex="-1" aria-labelledby="editConsultationTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header modal-header-lightblue">
                <h5 class="modal-title" id="editConsultationTitle">Edit Consultation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            @if($errors->any())
            <div class="alert alert-danger m-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="editConsultationFormModal" method="POST" action="{{ isset($cons) ? route('patients.consultations.update', [$patient, $cons]) : '#' }}">
                @csrf
                @method('PUT')

                @php
                $cons = isset($consultation) ? $consultation : null;
                $patientName = data_get($patient, 'full_name');
                if (!$patientName) {
                $ln = (string) (data_get($patient, 'last_name') ?? '');
                $fn = (string) (data_get($patient, 'first_name') ?? '');
                $patientName = trim($ln !== '' || $fn !== '' ? $ln.', '.$fn : '');
                }
                $outcomeVal = old('outcome', data_get($cons,'outcome'));
                $isSentHome = is_string($outcomeVal) && \Illuminate\Support\Str::startsWith($outcomeVal, 'sent home with:');
                $sentHomeDetails = $isSentHome ? trim(\Illuminate\Support\Str::after($outcomeVal, 'sent home with:')) : '';
                $lmpRaw = old('lmp', data_get($cons,'lmp'));
                $lmpDateVal = $lmpRaw ? \Carbon\Carbon::parse((string)$lmpRaw)->format('Y-m-d') : '';
                @endphp

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row g-4">
                            <!-- Left: vitals + complaint -->
                            <div class="col-12 col-xl-8">
                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <div class="mb-1">
                                            <label class="form-label">Chief Complaint</label>
                                            <input type="text" class="form-control" name="chief_complaint" maxlength="200" required
                                                placeholder="e.g., Headache, fever"
                                                value="{{ old('chief_complaint', data_get($cons,'chief_complaint')) }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Temperature (°C)</label>
                                            <input type="number" step="0.1" class="form-control" name="temperature" placeholder="e.g., 37.5"
                                                value="{{ old('temperature', data_get($cons,'temperature')) }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Pulse Rate (bpm)</label>
                                            <input type="number" class="form-control" name="pulse_rate" placeholder="e.g., 80"
                                                value="{{ old('pulse_rate', data_get($cons,'pulse_rate')) }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">SpO₂ (%)</label>
                                            <input type="number" class="form-control" name="spo2" placeholder="e.g., 98"
                                                value="{{ old('spo2', data_get($cons,'spo2')) }}">
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <div class="mb-1">
                                            <label class="form-label">Blood Pressure</label>
                                            <input type="text" class="form-control" name="blood_pressure" maxlength="20" placeholder="e.g., 120/80"
                                                value="{{ old('blood_pressure', data_get($cons,'blood_pressure')) }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Respiratory Rate (/min)</label>
                                            <input type="number" class="form-control" name="respiratory_rate" placeholder="e.g., 18"
                                                value="{{ old('respiratory_rate', data_get($cons,'respiratory_rate')) }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Last Menstrual Period</label>
                                            <input type="date" class="form-control" name="lmp" value="{{ $lmpDateVal }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Pain Scale</label>
                                            <input type="text" class="form-control" name="pain_scale" maxlength="50"
                                                value="{{ old('pain_scale', data_get($cons,'pain_scale')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: assessment / intervention / outcome -->
                            <div class="col-12 col-xl-4">
                                <div class="mb-1">
                                    <label class="form-label">Assessment</label>
                                    <textarea class="form-control" name="assessment" rows="4">{{ old('assessment', data_get($cons,'assessment')) }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Intervention</label>
                                    <textarea class="form-control" name="intervention" rows="4">{{ old('intervention', data_get($cons,'intervention')) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="row g-2">
                                    <!-- Outcome -->
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">Outcome</label>
                                        <select id="edit_outcome_select" class="form-select">
                                            <option value="">-- Select Outcome --</option>
                                            <option value="Improved, discharged" {{ old('outcome', data_get($cons,'outcome')) === 'Improved, discharged' ? 'selected' : '' }}>
                                                Improved, discharged
                                            </option>
                                            <option value="need further assessement and observation, discharged" {{ old('outcome', data_get($cons,'outcome')) === 'need further assessement and observation, discharged' ? 'selected' : '' }}>
                                                Needs further assessment, discharged
                                            </option>
                                            <option value="sent home with:" {{ $isSentHome ? 'selected' : '' }}>
                                                Sent home with…
                                            </option>
                                            <option value="referred to hospital of choice" {{ old('outcome', data_get($cons,'outcome')) === 'referred to hospital of choice' ? 'selected' : '' }}>
                                                Referred to hospital
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Sent Home Input -->
                                    <div class="col-12 col-md-6 mb-3 @if(!$isSentHome) d-none @endif" id="sentHomeGroup">
                                        <label class="form-label">Sent Home With</label>
                                        <input
                                            type="text"
                                            id="sent-home-input"
                                            class="form-control"
                                            placeholder="Enter the name"
                                            value="{{ $sentHomeDetails }}"
                                            data-init-show="{{ $isSentHome ? '1' : '0' }}"
                                            @if($isSentHome) required @endif>

                                        <input type="hidden" name="outcome" id="final_outcome_input" value="{{ e(old('outcome', data_get($cons,'outcome'))) }}">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Medicine Dispensing Section -->
                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header card-header-lightblue text-center">
                                        <h6 class="mb-0"><i class="fas fa-pills me-2"></i>Medicine Dispensing</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Previously Dispensed Medicines -->
                                        @php $dispensedMedicines = (isset($consultation) && $consultation) ? $consultation->getDispensedMedicinesFromTransactions() : []; @endphp
                                        @if(is_array($dispensedMedicines) && count($dispensedMedicines) > 0)
                                        <div class="alert alert-info mb-3">
                                            <h6 class="mb-2"><i class="fas fa-history me-2"></i>Previously Dispensed:</h6>
                                            @foreach($dispensedMedicines as $med)
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span>{{ $med['name'] ?? 'Unknown Medicine' }}</span>
                                                <span class="badge bg-secondary">{{ $med['quantity'] ?? 0 }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif

                                        <!-- Add Medicine Button -->
                                        <div class="text-center mb-3" id="editAddMedicineSection">
                                            <button type="button" class="btn-general btn-green btn-lg" id="editAddMedicineBtn">
                                                <i class="fas fa-plus-circle me-2"></i>Add Additional Medicine
                                            </button>
                                            <p class="text-muted mt-2 mb-0">Add more medicines to this consultation</p>
                                        </div>

                                        <!-- Medicine List Container -->
                                        <div id="editMedicineList"></div>

                                        <!-- Clean medicine data (accessible within modal) -->
                                        <div id="editMedicinesDataContainer" data-medicines="{{ json_encode($medicinesForJs ?? []) }}" style="display: none;"></div>

                                        <!-- Medicine Selection Template (Hidden) -->
                                        <div id="editMedicineTemplate" class="medicine-item border rounded p-4 mb-3" style="display: none;">
                                            <!-- Step 1: Search Medicine -->
                                            <div class="medicine-search-step">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 text-primary"><i class="fas fa-search me-2"></i>Step 1: Search Medicine</h6>
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-medicine">
                                                        <i class="fas fa-times"></i> Remove
                                                    </button>
                                                </div>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control form-control-lg medicine-search"
                                                        placeholder="Type medicine name (e.g., Paracetamol, Amoxicillin)..." autocomplete="off" disabled>
                                                    <input type="hidden" class="medicine-id" name="medicines[0][item_id]" disabled>
                                                    <div class="medicine-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 4px; max-height: 250px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                                    </div>
                                                </div>
                                                <small class="text-muted">Start typing to see available medicines with stock</small>
                                            </div>

                                            <!-- Step 2: Medicine Details (Hidden initially) -->
                                            <div class="medicine-details-step" style="display: none;">
                                                <hr class="my-4">
                                                <h6 class="mb-3 text-success"><i class="fas fa-check-circle me-2"></i>Step 2: Medicine Selected</h6>

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="selected-medicine-info p-3 bg-light rounded">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="medicine-name-display mb-1">-</h6>
                                                                    <div class="stock-info">
                                                                        <span class="badge bg-success stock-display">Stock: -</span>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-outline-secondary btn-sm change-medicine">
                                                                    <i class="fas fa-edit"></i> Change
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold">Quantity to Dispense</label>
                                                        <div class="input-group input-group-lg">
                                                            <input type="number" class="form-control quantity-input"
                                                                name="medicines[0][quantity]" min="1" placeholder="Enter amount" disabled>
                                                        </div>
                                                        <small class="text-muted">Enter the amount to give to patient</small>
                                                    </div>
                                                </div>

                                                <!-- Stock Warning -->
                                                <div class="stock-warning alert alert-danger mt-3" style="display: none;">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <span class="warning-message"></span>
                                                </div>

                                                <!-- Remaining Stock After Dispensing -->
                                                <div class="remaining-stock-info mt-3" style="display: none;">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <strong>After dispensing:</strong> <span class="remaining-stock-text"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Close</button>
                    <button class="btn-general btn-blue" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let editMedicineCounter = 0;

        // Medicine data from Laravel - loaded from data attribute
        const editMedicinesContainer = document.getElementById('editMedicinesDataContainer');
        const editMedicines = editMedicinesContainer ? JSON.parse(editMedicinesContainer.dataset.medicines) : [];

        // Add Medicine Button for Edit Modal
        const editAddMedicineBtn = document.getElementById('editAddMedicineBtn');
        if (editAddMedicineBtn) {
            editAddMedicineBtn.addEventListener('click', function() {
                addEditMedicineRow();

                // Hide the add button section after first medicine is added
                if (editMedicineCounter === 0) {
                    document.getElementById('editAddMedicineSection').style.display = 'none';
                }
            });
        }

        function addEditMedicineRow() {
            const template = document.getElementById('editMedicineTemplate');
            const clone = template.cloneNode(true);

            // Update IDs and names
            clone.id = 'edit-medicine-' + editMedicineCounter;
            clone.style.display = 'block';

            // Update form field names with counter
            const inputs = clone.querySelectorAll('input[name*="[0]"]');
            inputs.forEach(input => {
                input.name = input.name.replace('[0]', '[' + editMedicineCounter + ']');
            });

            // Enable all form fields in the cloned row (template has them disabled)
            const disabledFields = clone.querySelectorAll('input[disabled]');
            disabledFields.forEach(field => {
                field.removeAttribute('disabled');
                console.log('Enabled edit field:', field.name);
            });

            // Add event listeners
            setupEditMedicineRowEvents(clone);

            // Add to list
            document.getElementById('editMedicineList').appendChild(clone);

            // Focus on search input for immediate use
            setTimeout(() => {
                clone.querySelector('.medicine-search').focus();
            }, 100);

            editMedicineCounter++;
        }

        function setupEditMedicineRowEvents(row) {
            const searchInput = row.querySelector('.medicine-search');
            const medicineIdInput = row.querySelector('.medicine-id');
            const quantityInput = row.querySelector('.quantity-input');
            const dropdown = row.querySelector('.medicine-dropdown');
            const stockWarning = row.querySelector('.stock-warning');
            const warningMessage = row.querySelector('.warning-message');
            const removeBtn = row.querySelector('.remove-medicine');

            // New elements for improved UI
            const searchStep = row.querySelector('.medicine-search-step');
            const detailsStep = row.querySelector('.medicine-details-step');
            const medicineNameDisplay = row.querySelector('.medicine-name-display');
            const stockDisplay = row.querySelector('.stock-display');
            const changeMedicineBtn = row.querySelector('.change-medicine');
            const remainingStockInfo = row.querySelector('.remaining-stock-info');
            const remainingStockText = row.querySelector('.remaining-stock-text');

            let selectedMedicine = null;

            // Show all medicines when search input is focused (clicked)
            searchInput.addEventListener('focus', function() {
                if (this.value.trim() === '') {
                    showAllEditMedicines();
                }
            });

            // Medicine search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                if (searchTerm.length === 0) {
                    // Show all available medicines when input is empty
                    showAllEditMedicines();
                    return;
                }

                const filteredMedicines = editMedicines.filter(medicine =>
                    medicine.name.toLowerCase().includes(searchTerm) && medicine.stock > 0
                );

                displayEditMedicines(filteredMedicines);
            });

            // Function to show all available medicines
            function showAllEditMedicines() {
                const availableMedicines = editMedicines.filter(medicine => medicine.stock > 0);
                displayEditMedicines(availableMedicines);
            }

            // Function to display medicines in dropdown
            function displayEditMedicines(medicinesToShow) {
                if (medicinesToShow.length > 0) {
                    dropdown.innerHTML = '';
                    medicinesToShow.forEach(medicine => {
                        const item = document.createElement('div');
                        item.className = 'dropdown-item p-3 cursor-pointer';
                        item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">${medicine.name}</div>
                                    <small class="text-muted">Available: ${medicine.stock}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge ${medicine.stock > 10 ? 'bg-success' : medicine.stock > 5 ? 'bg-warning' : 'bg-danger'}">
                                        ${medicine.stock}
                                    </span>
                                </div>
                            </div>
                        `;
                        item.addEventListener('click', function() {
                            selectEditMedicine(medicine);
                        });
                        dropdown.appendChild(item);
                    });
                    dropdown.style.display = 'block';
                } else {
                    dropdown.innerHTML = '<div class="p-3 text-muted text-center">No medicines found with stock available</div>';
                    dropdown.style.display = 'block';
                }
            }

            function selectEditMedicine(medicine) {
                console.log('🎯 EDIT: SELECTING MEDICINE:', medicine);
                console.log('🔍 EDIT: Medicine ID Input Element:', medicineIdInput);
                console.log('🔍 EDIT: Medicine ID Input Name:', medicineIdInput ? medicineIdInput.name : 'NO INPUT');

                selectedMedicine = medicine;
                searchInput.value = medicine.name;
                medicineIdInput.value = medicine.id;

                console.log('✅ EDIT: SET MEDICINE ID:', medicineIdInput.value);
                console.log('✅ EDIT: MEDICINE ID INPUT NAME AFTER SET:', medicineIdInput.name);

                dropdown.style.display = 'none';

                // Update display elements
                medicineNameDisplay.textContent = medicine.name;
                stockDisplay.textContent = `Stock: ${medicine.stock}`;
                quantityInput.max = medicine.stock;

                // Set stock badge color
                stockDisplay.className = `badge ${medicine.stock > 10 ? 'bg-success' : medicine.stock > 5 ? 'bg-warning' : 'bg-danger'} stock-display`;

                // Show details step and hide search step
                searchStep.style.display = 'none';
                detailsStep.style.display = 'block';

                // Focus on quantity input
                quantityInput.focus();

                // Clear any previous warnings
                stockWarning.style.display = 'none';
                remainingStockInfo.style.display = 'none';
            }

            // Change medicine button
            changeMedicineBtn.addEventListener('click', function() {
                // Reset to search step
                detailsStep.style.display = 'none';
                searchStep.style.display = 'block';
                searchInput.value = '';
                searchInput.focus();

                // Reset form values
                medicineIdInput.value = '';
                quantityInput.value = '';
                selectedMedicine = null;

                // Hide warnings
                stockWarning.style.display = 'none';
                remainingStockInfo.style.display = 'none';
            });

            // Quantity validation and remaining stock calculation
            quantityInput.addEventListener('input', function() {
                console.log('📊 EDIT: QUANTITY INPUT CHANGED:', {
                    inputElement: this,
                    inputName: this.name,
                    inputValue: this.value,
                    selectedMedicine: selectedMedicine
                });

                if (!selectedMedicine) return;

                const requestedQty = parseInt(this.value) || 0;

                if (requestedQty > selectedMedicine.stock) {
                    stockWarning.style.display = 'block';
                    warningMessage.textContent = `Cannot dispense ${requestedQty}. Only ${selectedMedicine.stock} available.`;
                    this.setCustomValidity('Insufficient stock');
                    remainingStockInfo.style.display = 'none';
                } else if (requestedQty > 0) {
                    stockWarning.style.display = 'none';
                    this.setCustomValidity('');

                    // Show remaining stock calculation
                    const remaining = selectedMedicine.stock - requestedQty;
                    remainingStockText.textContent = `${remaining} will remain in stock`;
                    remainingStockInfo.style.display = 'block';
                } else {
                    stockWarning.style.display = 'none';
                    remainingStockInfo.style.display = 'none';
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!row.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // Remove medicine row
            removeBtn.addEventListener('click', function() {
                row.remove();

                // Show add button section if no medicines left
                const remainingRows = document.querySelectorAll('.medicine-item[id^="edit-medicine-"][style*="block"]');
                if (remainingRows.length === 0) {
                    document.getElementById('editAddMedicineSection').style.display = 'block';
                }
            });
        }

        // Form validation before submit
        const editForm = document.getElementById('editConsultationFormModal');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                console.log('🚀 EDIT FORM SUBMISSION STARTED 🚀');

                const medicineRows = document.querySelectorAll('.medicine-item[id^="edit-medicine-"][style*="block"]');
                let hasInvalidMedicine = false;

                console.log('📊 EDIT MEDICINE ROWS ANALYSIS:');
                console.log('- Total edit medicine rows:', medicineRows.length);

                let validMedicines = 0;
                medicineRows.forEach((row, index) => {
                    const medicineIdInput = row.querySelector('.medicine-id');
                    const quantityInput = row.querySelector('.quantity-input');
                    const medicineId = medicineIdInput ? medicineIdInput.value : '';
                    const quantity = parseInt(quantityInput ? quantityInput.value : '') || 0;

                    console.log(`🔍 Edit Medicine Row ${index}:`, {
                        rowId: row.id,
                        medicineIdInputExists: !!medicineIdInput,
                        quantityInputExists: !!quantityInput,
                        medicineIdInputName: medicineIdInput ? medicineIdInput.name : 'NO INPUT FOUND',
                        quantityInputName: quantityInput ? quantityInput.name : 'NO INPUT FOUND',
                        medicineIdValue: medicineId,
                        quantityValue: quantity,
                        isValidMedicine: !!(medicineId && quantity > 0)
                    });

                    if (medicineId && quantity > 0) {
                        validMedicines++;
                        const medicine = editMedicines.find(m => m.id == medicineId);
                        if (medicine && quantity > medicine.stock) {
                            hasInvalidMedicine = true;
                            alert(`Cannot dispense ${quantity} of ${medicine.name}. Only ${medicine.stock} available.`);
                        }
                    } else if (medicineId || quantity > 0) {
                        console.warn(`⚠️ Incomplete edit medicine data in row ${index}: ID=${medicineId}, Quantity=${quantity}`);
                    }
                });

                console.log(`✅ Valid edit medicines to dispense: ${validMedicines}`);

                // Log all form data being submitted
                const formData = new FormData(this);
                console.log('📝 ALL EDIT FORM DATA BEING SUBMITTED:');
                let medicineFieldCount = 0;
                for (let [key, value] of formData.entries()) {
                    if (key.includes('medicine')) {
                        console.log(`🏥 EDIT ${key} = "${value}"`);
                        medicineFieldCount++;
                    } else {
                        console.log(`   EDIT ${key} = "${value}"`);
                    }
                }
                console.log(`📊 Total edit medicine-related fields: ${medicineFieldCount}`);

                if (hasInvalidMedicine) {
                    console.error('❌ EDIT FORM SUBMISSION BLOCKED: Invalid medicine quantities');
                    e.preventDefault();
                } else {
                    console.log('✅ EDIT FORM SUBMISSION PROCEEDING');
                }
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Robust full-height scrollable edit modal */
    #editConsultationModal .modal-dialog {
        width: 100%;
        max-width: 1140px;
        height: calc(100dvh - 1rem);
        height: calc(100vh - 1rem);
        /* fallback */
        margin: 0.5rem auto;
        display: flex;
        flex-direction: column;
    }

    #editConsultationModal .modal-content {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        min-height: 0;
        max-height: 100%;
        overflow: hidden;
    }

    #editConsultationModal .modal-header,
    #editConsultationModal .modal-footer {
        flex-shrink: 0;
        position: sticky;
        background: #fff;
        z-index: 20;
    }

    #editConsultationModal .modal-header {
        top: 0;
    }

    #editConsultationModal .modal-footer {
        bottom: 0;
    }

    #editConsultationModal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        /* critical for Firefox */
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding-top: .75rem;
        padding-bottom: 4.5rem;
        /* space above sticky footer */
    }

    /* Scroll shadow feedback */
    #editConsultationModal .modal-header.shadow {
        box-shadow: 0 2px 4px rgba(0, 0, 0, .12);
    }

    #editConsultationModal .modal-footer.shadow {
        box-shadow: 0 -2px 4px rgba(0, 0, 0, .12);
    }

    @media (max-width: 576px) {

        #editConsultationModal .modal-header,
        #editConsultationModal .modal-footer {
            padding: .5rem .75rem;
        }

        #editConsultationModal .modal-body {
            padding: .5rem .75rem 4.25rem;
        }
    }

    /* Medicine Dispensing Styles for Edit Modal */
    .medicine-item {
        background-color: #f8f9fa;
        border: 2px solid #e9ecef !important;
        transition: all 0.3s ease;
    }

    .medicine-item:hover {
        border-color: #007bff !important;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.1);
    }

    .medicine-search:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .medicine-dropdown {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-top: none !important;
    }

    .dropdown-item {
        cursor: pointer;
        border-bottom: 1px solid #eee;
        padding: 12px 15px;
        transition: background-color 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        border-left: 3px solid #007bff;
    }

    .dropdown-item:last-child {
        border-bottom: none;
    }

    .selected-medicine-info {
        border-left: 4px solid #28a745;
    }

    .stock-display {
        font-size: 0.9rem;
    }

    .medicine-name-display {
        color: #495057;
        font-weight: 600;
    }

    .quantity-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .remaining-stock-info .alert {
        border-left: 4px solid #17a2b8;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Animation for step transitions */
    .medicine-details-step {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush