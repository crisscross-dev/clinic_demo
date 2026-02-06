<!-- Manage Category Modal (separated partial) -->
<!-- Manage Category Modal -->
<div class="modal fade" id="manageCategoryModal" tabindex="-1" aria-labelledby="manageCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="manageCategoryModalLabel">Manage Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Add Category Form -->
                <form action="{{ route('categories.store') }}" method="POST" class="mb-3 d-flex">
                    @csrf
                    <input type="text" name="name" class="form-control" placeholder="New Category Name" required>
                    <button type="submit" class="btn-general btn-green ms-2">Add</button>
                </form>

                <!-- Existing Categories -->
                <ul class="list-group">
                    @foreach($categories as $category)
                    <li class="list-group-item d-flex align-items-center">
                        <!-- Update Form -->
                        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="d-flex flex-grow-1">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $category->name }}" class="form-control form-control-sm">
                            <button type="submit" class="btn-general btn-blue ms-2">
                                <img src="{{ asset('icon/save.png') }}" title="save" alt="Save" width="16" height="16"> </button>
                        </form>

                        <!-- Delete Form -->
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="delete-form" data-delete-type="category">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-general btn-red ms-2">
                                <img src="{{ asset('icon/delete.png') }}" title="delete" alt="Delete" width="16" height="16">
                            </button>
                        </form>
                    </li>
                    @endforeach
                </ul>

            </div>

        </div>
    </div>
</div>