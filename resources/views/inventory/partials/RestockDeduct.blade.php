<!-- Restock Modal -->
<div class="modal fade" id="restockItemModal-{{ $item->id }}" tabindex="-1" aria-labelledby="restockItemModalLabel-{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-green">
                <h5 class="modal-title">Restock Item </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <input type="hidden" name="type" value="restock">
                <div class="modal-body">
                    <p>Current stock: <strong>{{ $item->total_stock }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Quantity to Add</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-general btn-green">Restock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deduct Modal -->
<div class="modal fade" id="deductItemModal-{{ $item->id }}" tabindex="-1" aria-labelledby="deductItemModalLabel-{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-red">
                <h5 class="modal-title">Deduct Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">

                <div class="modal-body">
                    <p>Current stock: <strong>{{ $item->total_stock }}</strong></p>

                    <div class="mb-3">
                        <label class="form-label">Quantity to Deduct</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <select class="form-select" name="type" required>
                            <option value="deduct">Used / Distributed</option>
                            <option value="expired">Expired</option>
                            <option value="lost">Lost</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-general btn-red">Confirm Deduction</button>
                </div>
            </form>
        </div>
    </div>
</div>