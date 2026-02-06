@push('styles')
@vite (['resources/css/inventory/report_modal.css'])
@endpush

<!-- Generate PDF Modal -->
<div class="modal fade" id="reportPdfModal" tabindex="-1" aria-labelledby="reportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header text-white">
                <h5 class="modal-title" id="reportPdfModalLabel">
                    <i class="fas fa-file-pdf"></i> Generate Inventory Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.inventory.report') }}" method="get" target="_blank">
                <div class="modal-body">

                    <!-- Date Range Picker -->
                    <div class="mb-3">
                        <label for="dateRangeBtn" class="form-label">Select Date Range</label>
                        <div>
                            <button type="button" id="dateRangeBtn" class="btn btn-outline-primary">Select Date</button>
                        </div>
                        <input type="hidden" name="start" id="startDate">
                        <input type="hidden" name="end" id="endDate">
                    </div>

                    <hr>

                    <!-- Checklist -->
                    <h6>Select Sections to Include:</h6>

                    <label class="checkbox">
                        <input class="form-check-input" type="checkbox" name="include_used" id="include_used" checked>
                        <span class="form-check-label">Used Items</span>
                    </label>

                    <label class="checkbox">
                        <input class="form-check-input" type="checkbox" name="include_restock" id="include_restock" checked>
                        <span class="form-check-label">Restocked Items</span>
                    </label>

                    <label class="checkbox">
                        <input class="form-check-input" type="checkbox" name="include_unused" id="include_unused" checked>
                        <span class="form-check-label">Least Usage</span>
                    </label>

                    <label class="checkbox">
                        <input class="form-check-input" type="checkbox" name="include_out" id="include_out" checked>
                        <span class="form-check-label">Out of Stock</span>
                    </label>

                    <label class="checkbox">
                        <input class="form-check-input" type="checkbox" name="include_low" id="include_low" checked>
                        <span class="form-check-label">Low Stock</span>
                    </label>

                    <label class="checkbox">
                        <input class="form-check-input" type="checkbox" name="include_lost_expired" id="include_lost_expired" checked>
                        <span class="form-check-label">Lost & Expired Items</span>
                    </label>

                    <label class="checkbox">
                        <input class="form-check-input" type="checkbox" name="include_transaction_log" id="include_transaction_log" checked>
                        <span class="form-check-label">Transaction Log (Restock & Deduct Records)</span>
                    </label>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-general btn-blue">
                        <i class="fas fa-file-download"></i> Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@vite('resources/js/inventory/report_modal.js')
@endpush