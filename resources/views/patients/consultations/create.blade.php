<!-- Create (Start) Consultation Modal (Bootstrap 5) -->
<div class="modal fade" id="createConsultationModal" tabindex="-1" aria-labelledby="createConsultationTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createConsultationTitle">Start New Consultation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            @if($errors->any() && session('create_consultation')) {{-- optional: set a session flag from controller when redirecting back from create --}}
            <div class="alert alert-danger m-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="createConsultationFormModal" method="POST" action="{{ route('patients.consultations.store', $patient) }}">
                @csrf

                @php
                $outcomeValCreate = old('outcome', '');
                $isSentHomeCreate = is_string($outcomeValCreate) && \Illuminate\Support\Str::startsWith($outcomeValCreate, 'sent home with:');
                $sentHomeDetailsCreate = $isSentHomeCreate ? trim(\Illuminate\Support\Str::after($outcomeValCreate, 'sent home with:')) : '';
                $lmpCreate = old('lmp') ? \Carbon\Carbon::parse(old('lmp'))->format('Y-m-d') : '';
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
                                                value="{{ old('chief_complaint') }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Temperature (°C)</label>
                                            <input type="number" step="0.1" class="form-control" name="temperature" placeholder="e.g., 37.5"
                                                value="{{ old('temperature') }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Pulse Rate (bpm)</label>
                                            <input type="number" class="form-control" name="pulse_rate" placeholder="e.g., 80"
                                                value="{{ old('pulse_rate') }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">SpO₂ (%)</label>
                                            <input type="number" class="form-control" name="spo2" placeholder="e.g., 98"
                                                value="{{ old('spo2') }}">
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <div class="mb-1">
                                            <label class="form-label">Blood Pressure</label>
                                            <input type="text" class="form-control" name="blood_pressure" maxlength="20" placeholder="e.g., 120/80"
                                                value="{{ old('blood_pressure') }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Respiratory Rate (/min)</label>
                                            <input type="number" class="form-control" name="respiratory_rate" placeholder="e.g., 18"
                                                value="{{ old('respiratory_rate') }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Last Menstrual Period</label>
                                            <input type="date" class="form-control" name="lmp" value="{{ $lmpCreate }}">
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label">Pain Scale</label>
                                            <input type="text" class="form-control" name="pain_scale" maxlength="50"
                                                value="{{ old('pain_scale') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: assessment / intervention / outcome -->
                            <div class="col-12 col-xl-4">
                                <div class="mb-1">
                                    <label class="form-label">Assessment</label>
                                    <textarea class="form-control" name="assessment" rows="4">{{ old('assessment') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Intervention</label>
                                    <textarea class="form-control" name="intervention" rows="4">{{ old('intervention') }}</textarea>
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
                                        <!-- Add Medicine Button -->
                                        <div class="text-center mb-3" id="addMedicineSection">
                                            <button type="button" class="btn-general btn-green btn-lg" id="addMedicineBtn">
                                                <i class="fas fa-plus-circle me-2"></i>Add Medicine to Dispense
                                            </button>
                                            <p class="text-muted mt-2 mb-0">Click to add medicines for this consultation</p>
                                        </div>

                                        <!-- Medicine List Container -->
                                        <div id="medicineList"></div>

                                        <!-- Clean medicine data (accessible within modal) -->
                                        <div id="medicinesDataContainer" data-medicines="{{ json_encode($medicinesForJs ?? []) }}" style="display: none;"></div>

                                        <!-- Medicine Selection Template (Hidden) -->
                                        <div id="medicineTemplate" class="medicine-item border rounded p-4 mb-3" style="display: none;">
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
                                                        name="medicine_search[0]" id="medicine_search_0" disabled
                                                        placeholder="Type medicine name (e.g., Paracetamol, Amoxicillin)..." autocomplete="off">
                                                    <input type="hidden" class="medicine-id" name="medicines[0][item_id]" id="medicine_item_id_0" disabled>
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
                                                        <label class="form-label fw-bold" for="medicine_quantity_0">Quantity to Dispense</label>
                                                        <div class="input-group input-group-lg">
                                                            <input type="number" class="form-control quantity-input"
                                                                name="medicines[0][quantity]" id="medicine_quantity_0" min="1" placeholder="Enter amount" disabled>
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
                    <button class="btn-general btn-blue" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>



@push('styles')
<style>
    .modal-body {
        max-height: calc(100vh - 160px);
        overflow-y: auto;
        padding-bottom: 1rem;
    }

    .modal-footer {
        position: sticky;
        bottom: 0;
        background: #fff;
        z-index: 10;
    }

    /* Medicine Dispensing Styles */
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let medicineCounter = 0;

        // Medicine data from Laravel - loaded from data attribute
        const medicinesContainer = document.getElementById('medicinesDataContainer');
        const medicines = medicinesContainer ? JSON.parse(medicinesContainer.dataset.medicines) : [];

        // Add Medicine Button
        document.getElementById('addMedicineBtn').addEventListener('click', function() {
            addMedicineRow();

            // Hide the add button section after first medicine is added
            if (medicineCounter === 1) {
                document.getElementById('addMedicineSection').style.display = 'none';
            }
        });

        function addMedicineRow() {
            const template = document.getElementById('medicineTemplate');
            const clone = template.cloneNode(true);

            // Update IDs and names
            clone.id = 'medicine-' + medicineCounter;
            clone.style.display = 'block';

            // Update form field names and IDs with counter
            const inputs = clone.querySelectorAll('input[name*="[0]"], input[id*="_0"]');
            const labels = clone.querySelectorAll('label[for*="_0"]');

            console.log('Found inputs to update:', inputs.length);
            console.log('Found labels to update:', labels.length);

            inputs.forEach(input => {
                const oldName = input.name;
                const oldId = input.id;

                // Update name attribute
                if (input.name) {
                    input.name = input.name.replace('[0]', '[' + medicineCounter + ']');
                }

                // Update id attribute  
                if (input.id) {
                    input.id = input.id.replace('_0', '_' + medicineCounter);
                }

                console.log('Updated field:', {
                    oldName: oldName,
                    newName: input.name,
                    oldId: oldId,
                    newId: input.id
                });
            });

            // Update label for attributes
            labels.forEach(label => {
                const oldFor = label.getAttribute('for');
                if (oldFor) {
                    label.setAttribute('for', oldFor.replace('_0', '_' + medicineCounter));
                    console.log('Updated label for:', oldFor, '->', label.getAttribute('for'));
                }
            });

            // Enable all form fields in the cloned row (template has them disabled)
            const disabledFields = clone.querySelectorAll('input[disabled]');
            disabledFields.forEach(field => {
                field.removeAttribute('disabled');
                console.log('Enabled field:', field.name);
            });

            // Add event listeners
            setupMedicineRowEvents(clone);

            // Add to list
            document.getElementById('medicineList').appendChild(clone);

            // Focus on search input for immediate use
            setTimeout(() => {
                clone.querySelector('.medicine-search').focus();
            }, 100);

            medicineCounter++;
        }

        function setupMedicineRowEvents(row) {
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
                    showAllMedicines();
                }
            });

            // Medicine search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                if (searchTerm.length === 0) {
                    // Show all available medicines when input is empty
                    showAllMedicines();
                    return;
                }

                const filteredMedicines = medicines.filter(medicine =>
                    medicine.name.toLowerCase().includes(searchTerm) && medicine.stock > 0
                );

                displayMedicines(filteredMedicines);
            });

            // Function to show all available medicines
            function showAllMedicines() {
                const availableMedicines = medicines.filter(medicine => medicine.stock > 0);
                displayMedicines(availableMedicines);
            }

            // Function to display medicines in dropdown
            function displayMedicines(medicinesToShow) {
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
                            selectMedicine(medicine);
                        });
                        dropdown.appendChild(item);
                    });
                    dropdown.style.display = 'block';
                } else {
                    dropdown.innerHTML = '<div class="p-3 text-muted text-center">No medicines found with stock available</div>';
                    dropdown.style.display = 'block';
                }
            }

            function selectMedicine(medicine) {
                console.log('🎯 SELECTING MEDICINE:', medicine);
                console.log('🔍 Medicine ID Input Element:', medicineIdInput);
                console.log('🔍 Medicine ID Input Name:', medicineIdInput ? medicineIdInput.name : 'NO INPUT');

                selectedMedicine = medicine;
                searchInput.value = medicine.name;
                medicineIdInput.value = medicine.id;

                console.log('✅ SET MEDICINE ID:', medicineIdInput.value);
                console.log('✅ MEDICINE ID INPUT NAME AFTER SET:', medicineIdInput.name);

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
                console.log('📊 QUANTITY INPUT CHANGED:', {
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
                const remainingRows = document.querySelectorAll('.medicine-item[style*="block"]');
                if (remainingRows.length === 0) {
                    document.getElementById('addMedicineSection').style.display = 'block';
                }
            });
        }

        // Form validation before submit
        document.getElementById('createConsultationFormModal').addEventListener('submit', function(e) {
            console.log('🚀 FORM SUBMISSION STARTED 🚀');

            // Find all medicine rows that are visible (not the template)
            const medicineRows = document.querySelectorAll('.medicine-item:not(#medicineTemplate)');
            const visibleMedicineRows = Array.from(medicineRows).filter(row => row.style.display !== 'none');
            let hasInvalidMedicine = false;

            console.log('📊 MEDICINE ROWS ANALYSIS:');
            console.log('- Total medicine rows:', medicineRows.length);
            console.log('- Visible medicine rows:', visibleMedicineRows.length);

            let validMedicines = 0;
            visibleMedicineRows.forEach((row, index) => {
                const medicineIdInput = row.querySelector('.medicine-id');
                const quantityInput = row.querySelector('.quantity-input');
                const medicineId = medicineIdInput ? medicineIdInput.value : '';
                const quantity = parseInt(quantityInput ? quantityInput.value : '') || 0;

                console.log(`🔍 Medicine Row ${index}:`, {
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
                    const medicine = medicines.find(m => m.id == medicineId);
                    if (medicine && quantity > medicine.stock) {
                        hasInvalidMedicine = true;
                        alert(`Cannot dispense ${quantity} of ${medicine.name}. Only ${medicine.stock} available.`);
                    }
                } else if (medicineId || quantity > 0) {
                    console.warn(`⚠️ Incomplete medicine data in row ${index}: ID=${medicineId}, Quantity=${quantity}`);
                }
            });

            console.log(`✅ Valid medicines to dispense: ${validMedicines}`);

            // Log all form data being submitted
            const formData = new FormData(this);
            console.log('📝 ALL FORM DATA BEING SUBMITTED:');
            let medicineFieldCount = 0;
            for (let [key, value] of formData.entries()) {
                if (key.includes('medicine')) {
                    console.log(`🏥 ${key} = "${value}"`);
                    medicineFieldCount++;
                } else {
                    console.log(`   ${key} = "${value}"`);
                }
            }
            console.log(`📊 Total medicine-related fields: ${medicineFieldCount}`);

            if (hasInvalidMedicine) {
                console.error('❌ FORM SUBMISSION BLOCKED: Invalid medicine quantities');
                e.preventDefault();
            } else {
                console.log('✅ FORM SUBMISSION PROCEEDING');
            }
        });
    });
</script>
@endpush