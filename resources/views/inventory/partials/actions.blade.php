<div class="dropdown position-static actions-dropdown">
    <style>
        .actions-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
        }

        .actions-icon-btn i {
            color: #495057;
        }

        .actions-icon-btn:hover i {
            color: #0d6efd;
        }
    </style>
    <button type="button"
        class="btn btn-sm btn-soft-primary actions-icon-btn p-0 rounded-circle d-flex align-items-center justify-content-center"
        data-bs-toggle="dropdown"
        data-bs-boundary="viewport"
        data-bs-reference="parent"
        aria-expanded="false"
        aria-haspopup="true"
        aria-label="Actions"
        title="Actions">
        <span class="visually-hidden">Actions</span>
        <i class="bi bi-gear-fill fs-5 icon-custom" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 py-2">
        <!-- Edit -->
        <li>
            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#editItemModal-{{ $item->id }}">
                <i class="fas fa-edit me-2 text-secondary"></i> Edit
            </a>
        </li>

        <!-- Restock -->
        <li>
            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#restockItemModal-{{ $item->id }}">
                <i class="fas fa-box me-2 text-secondary"></i> Restock
            </a>
        </li>

        <!-- Deduct -->
        <li>
            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#deductItemModal-{{ $item->id }}">
                <i class="fas fa-minus-circle me-2 text-secondary"></i> Deduct
            </a>
        </li>

        <li>
            <hr class="dropdown-divider my-1">
        </li>

        <!-- Delete -->
        <li>
            <form action="{{ route('inventory.destroy', $item->id) }}"
                method="POST"
                data-delete-type="InventoryItem"
                class="delete-form m-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                    <i class="fas fa-trash-alt me-2"></i> Delete
                </button>
            </form>
        </li>
    </ul>
</div>