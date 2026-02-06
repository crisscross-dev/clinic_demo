@extends('layouts.app')

@section('title', 'Dashboard - Samuel Clinic')

@push('styles')
@vite('resources/css/admin/dashboard.css')
@endpush

@section('content')
<div class="main-content">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1>Welcome back, {{ $admin->full_name ?? session('admin_firstname', 'Admin') }}!</h1>
        <p>Here's what's happening at Demo Clinic today</p>
    </div>

    <!-- Include Flash Messages -->
    @include('partials.flash-messages')



    <!-- Trend (Consultations) for Logged-in Admin Only -->
    <div class="section-title"><i class="fas fa-user-md"></i>Consultations Statistics</div>
    <div class="dashboard-card" style="margin-top:2rem;">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h3><i class="fas fa-user-md"></i> Your Consultations - {{ $admin->full_name ?? '' }} </h3>

            <div class="d-flex flex-column align-items-md-end">
                <div class="d-flex align-items-center gap-2">
                    <!-- Fake button (user clicks this) -->
                    <button id="date-btn-admin" type="button" class="btn-general btn-blue">
                        <i class="fas fa-calendar-alt"></i> Select Date
                    </button>

                    <!-- PDF Download Button for Admin's own consultations -->
                    <button id="download-admin-pdf-btn" class="btn-general btn-green" onclick="downloadAdminConsultationsPdf()">
                        <i class="fas fa-download"></i> Download PDF
                    </button>

                    <!-- Litepicker attaches to the fake button; hidden start/end inputs keep selected values -->

                    <!-- Hidden inputs for backend -->
                    <input type="hidden" id="consult-start-admin" name="consult_start_admin">
                    <input type="hidden" id="consult-end-admin" name="consult_end_admin">
                    <!-- Hidden input for admin id -->
                    <input type="hidden" id="admin-id" value="{{ session('admin_id') }}">
                </div>
            </div>
        </div>

        <!-- Centered Selected Date -->
        <div class="text-center mt-2">
            <span id="selected-date-text-admin" class="fw-bold"></span>
        </div>

        <div class="card-content">
            <div class="chart-wrapper">
                <canvas id="registrations7d-admin"></canvas>
            </div>
            <div id="no-consultations-admin" class="empty-state" style="display:none;">
                <i class="fas fa-stethoscope"></i>
                <p>No consultations for selected date</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="section-title"><i class="fas fa-user"></i> Patients</div>
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-users stat-icon"></i>
            <h3>Total Patients</h3>
            <p class="stat-number">{{ number_format($totalPatients ?? 0) }}</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-user-clock stat-icon"></i>
            <h3>Pending Requests</h3>
            <p class="stat-number">{{ number_format($totalPendingEmails ?? 0) }}</p>
        </div>
    </div>

    <div class="section-title" style="margin-top:1rem;"><i class="fas fa-stethoscope"></i> Consultations</div>
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-calendar-day stat-icon"></i>
            <h3>Consultations Today</h3>
            <p class="stat-number">{{ number_format($todayConsultations ?? 0) }}</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-week stat-icon"></i>
            <h3>Consultations This Week</h3>
            <p class="stat-number">{{ number_format($thisWeekRegistrations ?? 0) }}</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-calendar-alt stat-icon"></i>
            <h3>Consultations This Month</h3>
            <p class="stat-number">{{ number_format($thisMonthRegistrations ?? 0) }}</p>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="dashboard-grid">
        <!-- Recent Consultations -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-stethoscope"></i> Recent Consultations</h3>
            </div>
            <div class="card-content">
                @if(!empty($recentConsultations) && count($recentConsultations) > 0)
                <ul class="recent-patients-list">
                    @foreach($recentConsultations as $consult)
                    <li>
                        <div class="patient-info">
                            <div class="patient-name">
                                {{ optional($consult->patient)->first_name }} {{ optional($consult->patient)->middle_name }} {{ optional($consult->patient)->last_name }}
                            </div>
                            <div class="patient-meta">
                                {{ optional($consult->patient)->course }} - {{ optional($consult->patient)->year_level }} | {{ optional($consult->patient)->department }}
                            </div>
                        </div>
                        <div class="patient-date">
                            {{ $consult->created_at->diffForHumans() }}
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="empty-state">
                    <i class="fas fa-stethoscope"></i>
                    <p>No consultations yet</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Department Statistics (Consultations This Month) -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-pie"></i> Top Departments (Consultations This Month)</h3>
            </div>
            <div class="card-content">
                @if(!empty($departmentStats) && count($departmentStats) > 0)
                <ul class="stats-list">
                    @foreach($departmentStats as $dept)
                    <li>
                        <span class="stat-label">{{ $dept->department ?: 'Not Specified' }}</span>
                        <span class="stat-value">{{ $dept->count }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="empty-state">
                    <i class="fas fa-chart-pie"></i>
                    <p>No department consultation data this month</p>
                </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Trend (Consultations) with Timeframe -->
    <div class="section-title"><i class="fas fa-chart-line"></i> Consultations Statistics</div>
    <div class="dashboard-card" style="margin-top:2rem;">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h3><i class="fas fa-stethoscope"></i>All Consultations</h3>

            <div class="d-flex flex-column align-items-md-end">
                <div class="d-flex align-items-center gap-2">
                    <!-- Fake button (user clicks this) -->
                    <button id="date-btn" class="btn-general btn-blue">
                        <i class="fas fa-calendar-alt"></i> Select Date
                    </button>

                    <!-- PDF Download Button -->
                    <button id="download-pdf-btn" class="btn-general btn-green" onclick="downloadConsultationsPdf()">
                        <i class="fas fa-download"></i> Download PDF
                    </button>

                    <!-- Hidden inputs for backend -->
                    <input type="hidden" id="consult-start" name="consult_start">
                    <input type="hidden" id="consult-end" name="consult_end">
                </div>
            </div>
        </div>

        <!-- Centered Selected Date -->
        <div class="text-center mt-2">
            <span id="selected-date-text" class="fw-bold"></span>
        </div>

        <div class="card-content">
            <div class="chart-wrapper">
                <canvas id="registrations7d"></canvas>
            </div>
        </div>
    </div>






    <!-- Inventory Usage (Compact) -->
    <div class="section-title"><i class="fas fa-pills"></i> Inventory Usage</div>
    <div class="dashboard-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-box-open"></i> Most Used Items</h3>
            <div class="btn-group btn-group-sm" role="group" aria-label="Usage range">
                <button type="button" class="btn-toggle btn-toggle-blue usage-range active" data-range="today">
                    Today
                </button>
                <button type="button" class="btn-toggle btn-toggle-blue usage-range" data-range="7d">
                    Last 7 days
                </button>
                <button type="button" class="btn-toggle btn-toggle-blue usage-range" data-range="30d">
                    Last 30 days
                </button>
            </div>

        </div>
        <div class="card-content">
            @if(isset($inventoryUsage) && $inventoryUsage->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Used</th>
                            <th class="text-end">Stock Remaining</th>
                            <th class="text-center">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryUsage->sortByDesc('month_used') as $u)
                        @php
                        $todayUsed = (int) ($u->today_used ?? 0);
                        $weekUsed = (int) ($u->week_used ?? 0);
                        $monthUsed = (int) ($u->month_used ?? 0);
                        $totalStock = (int) ($u->total_stock ?? 0);
                        $threshold = (int) ($u->low_stock_reminder ?? 5);

                        if ($totalStock <= 0) {
                            $remarkText="Out of Stock" ;
                            $remarkClass="bg-danger text-white" ;
                            } elseif ($totalStock < $threshold) {
                            $remarkText="Low Stock" ;
                            $remarkClass="bg-warning text-dark" ;
                            } else {
                            $remarkText="In Stock" ;
                            $remarkClass="bg-success text-white" ;
                            }
                            @endphp

                            <tr class="usage-row"
                            data-today="{{ $todayUsed }}"
                            data-7d="{{ $weekUsed }}"
                            data-30d="{{ $monthUsed }}"
                            data-stock="{{ $totalStock }}"
                            data-threshold="{{ $threshold }}">
                            <td>
                                <strong>{{ $u->name }}</strong>
                            </td>
                            <td class="text-end usage-cell">{{ $todayUsed }}</td>
                            <td class="text-end">{{ number_format($totalStock) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $remarkClass }}">{{ $remarkText }}</span>
                            </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>No inventory usage recorded yet</p>
            </div>
            @endif
        </div>
    </div>




    <!-- Inventory Chart (Horizontal Stacked) -->
    <div class="section-title"><i class="fas fa-chart-bar"></i> Inventory Chart</div>
    <div class="dashboard-card" style="margin-top:1rem;">
        <div class="card-header justify-content-between">
            <h3 class="mb-0"><i class="fas fa-boxes"></i> Most Used Items</h3>
            <div class="d-flex align-items-center gap-2">
                <!-- Fake button for picker -->
                <button id="inv-date-btn" type="button" class=" btn-general btn-blue">
                    <i class="fas fa-calendar-alt"></i> Select Date
                </button>

                <!-- Hidden fields for backend -->
                <input type="hidden" id="inv-start" />
                <input type="hidden" id="inv-end" />
            </div>
        </div>

        <!-- Centered selected date range (like consultations) -->
        <div class="text-center mt-2">
            <span id="inv-date-range" class="fw-bold"></span>
        </div>

        <div class="card-content">
            <div class="chart-wrapper">
                <canvas id="chart-inventory"></canvas>
            </div>
        </div>
    </div>


    @endsection

    @push('scripts')
    <script>
        window.dashboardRoutes = {
            consultationsSeries: "{{ route('admin.dashboard.consultationsSeries') }}",
            inventorySeries: "{{ route('admin.dashboard.inventorySeries') }}"
        };

        // Function to download consultations PDF with date filters
        function downloadConsultationsPdf() {
            const startDate = document.getElementById('consult-start').value;
            const endDate = document.getElementById('consult-end').value;

            // Build URL with query parameters
            let url = "{{ route('admin.consultations.pdf') }}";
            const params = new URLSearchParams();

            if (startDate) {
                params.append('date_from', startDate);
            }
            if (endDate) {
                params.append('date_to', endDate);
            }

            // Add other potential filters (you can expand this)
            // params.append('department', 'Engineering'); // Example
            // params.append('course', 'IT'); // Example

            if (params.toString()) {
                url += '?' + params.toString();
            }

            // Open download in new window/tab
            window.open(url, '_blank');
        }

        // Function to download admin's personal consultations PDF
        function downloadAdminConsultationsPdf() {
            const startDate = document.getElementById('consult-start-admin').value;
            const endDate = document.getElementById('consult-end-admin').value;
            const adminId = document.getElementById('admin-id').value;

            // Build URL with query parameters
            let url = "{{ route('admin.consultations.pdf') }}";
            const params = new URLSearchParams();

            if (startDate) {
                params.append('date_from', startDate);
            }
            if (endDate) {
                params.append('date_to', endDate);
            }

            // Filter by admin ID for personal consultations only
            if (adminId) {
                params.append('admin_id', adminId);
            }

            if (params.toString()) {
                url += '?' + params.toString();
            }

            // Open download in new window/tab
            window.open(url, '_blank');
        }
    </script>
    @vite('resources/js/admin/dashboard.js')
    @endpush