<div class="modal fade" id="editItemModal-{{ $item->id }}" tabindex="-1" aria-labelledby="editItemModalLabel-{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel-{{ $item->id }}">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('inventory.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $item->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-control" name="category_id" required>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Low Stock Reminder</label>
                            <input type="number" class="form-control" name="low_stock_reminder" min="0" value="{{ $item->low_stock_reminder ?? 0 }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-general btn-blue">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>