<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Add New Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <!-- 🔔 Low Stock Reminder Input -->
                        <div class="col-md-12 mb-3">
                            <label for="low_stock_reminder" class="form-label">Low Stock Reminder</label>
                            <input type="number" class="form-control" id="low_stock_reminder" name="low_stock_reminder" min="1" value="5" required>
                        </div>
                    </div>

                    <!-- total_stock and status are set server-side -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-general btn-blue">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>