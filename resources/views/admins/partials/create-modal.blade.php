@push('styles')
<style>
    .modal-header {
        background: linear-gradient(to bottom right, #cc5c1a, #ffa64d);
        /* lighter burnt orange → lighter strong orange */
        color: #fff;
    }

    .modal-content {
        background: #fffaf5;
        /* warm off-white */
        color: #333;

        .form-control {
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 4px 8px rgba(168, 67, 0, 0.3);
            /* stronger shadow on focus */
            border-color: #ff8c00;
            /* matches orange theme */
            outline: none;
        }


    }
</style>
@endpush

<!-- Create Admin Modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1" aria-labelledby="createAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-bg-color">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAdminModalLabel">
                    <i class="bi bi-person-plus me-2"></i>
                    Create New Admin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createAdminForm" method="POST" action="{{ route('admins.store') }}">
                <div class="modal-body">
                    @csrf

                    <!-- Error container for modal -->
                    <div id="modalErrors" class="alert alert-danger d-none">
                        <ul class="mb-0" id="errorList"></ul>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_firstname" class="form-label">First Name</label>
                            <input type="text"
                                class="form-control @error('firstname') is-invalid @enderror"
                                id="modal_firstname"
                                name="firstname"
                                value="{{ old('firstname') }}"
                                required>
                            @error('firstname')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @else
                            <div class="invalid-feedback"></div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="modal_lastname" class="form-label">Last Name</label>
                            <input type="text"
                                class="form-control @error('lastname') is-invalid @enderror"
                                id="modal_lastname"
                                name="lastname"
                                value="{{ old('lastname') }}"
                                required>
                            @error('lastname')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @else
                            <div class="invalid-feedback"></div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="modal_username" class="form-label">Email/Username</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text"
                                    class="form-control @error('username') is-invalid @enderror"
                                    id="modal_username"
                                    name="username"
                                    value="{{ old('username') }}"
                                    required
                                    autocomplete="off">
                            </div>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @else
                            <div class="invalid-feedback"></div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="modal_role" class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror"
                                id="modal_role"
                                name="role"
                                required>
                                <option value="">Select a role</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="medical" {{ old('role') === 'medical' ? 'selected' : '' }}>Medical</option>
                                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @else
                            <div class="invalid-feedback"></div>
                            @enderror
                        </div>

                        <!-- Password will be set to default automatically -->
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Default Password:</strong> The new admin will be created with the default password: <code>Samuelclinic_2012</code><br>
                                <small>They can change it after logging in through their settings.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-general btn-orange" id="submitBtn">
                        <i class="bi bi-person-plus me-1"></i>
                        Create Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Password toggle styles */
    .input-wrapper {
        position: relative;
    }

    .toggle-eye-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: none;
        z-index: 3;
        font-size: 16px;
    }

    .toggle-eye-btn:hover {
        color: #495057;
    }

    .toggle-eye-btn:focus {
        outline: none;
    }

    .input-wrapper .form-control {
        padding-right: 45px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = '{{ csrf_token() }}';

        // Handle Create Admin Modal Form Submission
        const createAdminForm = document.getElementById('createAdminForm');
        if (createAdminForm) {
            createAdminForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('submitBtn');
                const modalErrors = document.getElementById('modalErrors');
                const errorList = document.getElementById('errorList');

                // Disable button during submission
                submitBtn.disabled = true;
                modalErrors.classList.add('d-none');

                // Clear previous validation errors
                const inputs = createAdminForm.querySelectorAll('.form-control, .form-select');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                    const container = input.closest('.col-md-6, .col-12, .col-6');
                    if (container) {
                        const feedback = container.querySelector('.invalid-feedback');
                        if (feedback) feedback.textContent = '';
                    }
                });

                // Submit form with timeout
                const formData = new FormData(createAdminForm);
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000); // 15s timeout

                fetch(createAdminForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData,
                        signal: controller.signal
                    })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 422) {
                                // Handle validation errors
                                return response.json().then(errorData => {
                                    throw {
                                        status: 422,
                                        errors: errorData.errors
                                    };
                                });
                            }
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Success - close modal and redirect to show flash message
                            const modalEl = document.getElementById('createAdminModal');
                            let modal = null;
                            if (window.bootstrap && window.bootstrap.Modal) {
                                modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                                modal.hide();
                            } else if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
                                // Fallback if using jQuery bootstrap
                                $(modalEl).modal('hide');
                            }

                            // Redirect to refresh and show server flash message
                            window.location.href = '{{ route("admins.index") }}';
                        } else {
                            // Handle validation errors
                            if (data.errors) {
                                // Show field-specific errors
                                Object.keys(data.errors).forEach(field => {
                                    const input = document.getElementById(`modal_${field}`);
                                    if (input) {
                                        input.classList.add('is-invalid');
                                        const container = input.closest('.col-md-6, .col-12, .col-6');
                                        if (container) {
                                            const feedback = container.querySelector('.invalid-feedback');
                                            if (feedback) feedback.textContent = data.errors[field][0];
                                        }
                                    }
                                });

                                // Show general error list
                                errorList.innerHTML = '';
                                Object.values(data.errors).flat().forEach(error => {
                                    const li = document.createElement('li');
                                    li.textContent = error;
                                    errorList.appendChild(li);
                                });
                                modalErrors.classList.remove('d-none');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        if (error.name === 'AbortError') {
                            console.error('Request timed out');
                            return;
                        }

                        // Handle validation errors (422)
                        if (error.status === 422 && error.errors) {
                            // Show field-specific errors
                            Object.keys(error.errors).forEach(field => {
                                const input = document.getElementById(`modal_${field}`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const container = input.closest('.col-md-6, .col-12, .col-6');
                                    if (container) {
                                        const feedback = container.querySelector('.invalid-feedback');
                                        if (feedback) feedback.textContent = error.errors[field][0];
                                    }
                                }
                            });

                            // Show general error list
                            errorList.innerHTML = '';
                            Object.values(error.errors).flat().forEach(errorMsg => {
                                const li = document.createElement('li');
                                li.textContent = errorMsg;
                                errorList.appendChild(li);
                            });
                            modalErrors.classList.remove('d-none');
                            return;
                        }

                        // Handle other error types
                        if (error.message && error.message.includes('500')) {
                            console.error('Server error occurred');
                        } else {
                            console.error('Network error occurred');
                        }
                    })
                    .finally(() => {
                        clearTimeout(timeoutId);
                        // Reset button state
                        submitBtn.disabled = false;
                    });
            });
        }

        // Handle password toggle buttons (using create blade structure)
        function toggleVisibility(input, button) {
            if (!input || !button) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.innerHTML = isHidden ?
                '<i class="bi bi-eye-slash"></i>' :
                '<i class="bi bi-eye"></i>';
        }

        function attachToggle(inputId, btnId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(btnId);

            if (!input || !button) return;

            // Show/hide eye based on input value
            input.addEventListener('input', function() {
                button.style.display = this.value.length > 0 ? 'block' : 'none';
            });

            // Toggle password visibility
            button.addEventListener('click', function() {
                toggleVisibility(input, this);
            });
        }

        // Password fields removed - no toggle needed

        // Reset modal form when closed
        const createAdminModal = document.getElementById('createAdminModal');
        if (createAdminModal) {
            createAdminModal.addEventListener('hidden.bs.modal', function() {
                createAdminForm.reset();

                // Clear validation errors
                const inputs = createAdminForm.querySelectorAll('.form-control, .form-select');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                    const container = input.closest('.col-md-6, .col-12, .col-6');
                    if (container) {
                        const feedback = container.querySelector('.invalid-feedback');
                        if (feedback) feedback.textContent = '';
                    }
                });

                // Hide error container
                document.getElementById('modalErrors').classList.add('d-none');

                // Password fields removed - no reset needed
            });
        }
    });
</script>
@endpush