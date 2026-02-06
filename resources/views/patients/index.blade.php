    @extends('layouts.app')

    @push('styles')
    @vite([
    'resources/css/patients/patient-index.css',
    ])
    @endpush

    @section('content')
    <div class="main-content">
        {{-- Controls: Department filter (left) + Search (right) --}}
        <div class="list-header d-flex justify-content-between align-items-center flex-wrap mb-3">
            <!-- Left Section -->
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Department Dropdown -->
                <div class="dropdown">
                    <button
                        class="btn-general btn-blue dropdown-toggle"
                        type="button"
                        id="departmentDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ request('department') ?: 'All Departments' }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="departmentDropdown">
                        <li>
                            <a class="dropdown-item department-filter" href="#" data-department="">All Departments</a>
                        </li>
                        @if(isset($departments) && count($departments))
                        @foreach($departments as $dept)
                        <li>
                            <a class="dropdown-item department-filter" href="#" data-department="{{ $dept }}">
                                {{ $dept }}
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Header Meta -->
                <div class="header-meta d-flex align-items-center gap-2 flex-wrap">
                    <strong id="departmentLabel">{{ request('department') ?: 'All' }}</strong>
                    <span class="text-muted">|</span>
                    <strong id="patientCount">
                        {{ method_exists($patients, 'total') ? $patients->total() : $patients->count() }}
                    </strong>
                </div>

            </div>

            <!-- Right Section -->
            <form method="GET" action="{{ route('patients.index') }}" class="d-flex gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search patients..."
                    class="form-control-head" />
                <button type="submit" class=" btn-general btn-blue">Search</button>
            </form>
        </div>

        {{-- Patients Table --}}
        <div class="records-scrollable">
            <div class="table-container">
                <table class="patient-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Course/Section</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    {{-- Header Section moved below table header --}}
                    <tbody>
                        @forelse($patients as $patient)
                        <tr data-patient-id="{{ $patient->id }}" style="cursor: pointer;" title="Double-click to view details">
                            <td>{{ $patients->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="text-truncate">
                                    {{ $patient->full_name }}
                                </div>
                            </td>
                            <td>{{ $patient->department ?: '—' }}</td>
                            <td>
                                {{ $patient->course }}
                                @if($patient->year_level)
                                <span class="text-muted">/ {{ $patient->year_level }}</span>
                                @endif
                            </td>
                            <td>{{ $patient->contact_no ?: '—' }}</td>
                            <td>
                                <div class="text-truncate">{{ $patient->address ?: '—' }}</div>
                            </td>
                            <td>
                                <div class="actions-dropdown">
                                    <button type="button" class="actions-toggle" aria-haspopup="true" aria-expanded="false" title="Actions">
                                        <i class="bi bi-gear-fill fs-5 icon-custom"></i>
                                    </button>
                                    <div class="actions-menu" role="menu" style="display:none;">
                                        @include('patients/partials.action-menu', ['patient' => $patient])
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <p>No student patients found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Controls --}}
            @if($patients instanceof \Illuminate\Pagination\LengthAwarePaginator && $patients->hasPages())
            <div class="pagination-footer">
                {{ $patients->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
            </div>
            @endif



        </div>
    </div>
    @endsection

    @push('scripts')
    @vite([
    'resources/js/patients/index_patients.js',
    ])
    <script>
        // Double-click to view patient details
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.patient-table tbody tr[data-patient-id]').forEach(function(row) {
                row.addEventListener('dblclick', function(e) {
                    // Don't trigger if clicking on action buttons
                    if (e.target.closest('.actions-dropdown')) {
                        return;
                    }

                    var patientId = this.getAttribute('data-patient-id');
                    if (patientId) {
                        window.location.href = '/patients/' + patientId;
                    }
                });
            });
        });

        document.querySelectorAll('.department-filter').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                var dept = this.getAttribute('data-department');
                var params = new URLSearchParams(window.location.search);
                if (dept) {
                    params.set('department', dept);
                } else {
                    params.delete('department');
                }
                params.delete('page'); // Always reset to first page
                window.location.href = "{{ route('patients.index') }}" + (params.toString() ? '?' + params.toString() : '');
            });
        });
    </script>
    @endpush