@extends('layouts.app')
@push('styles')
@vite(['resources/css/inventory/index.css'])
@endpush

@section('title', 'Medical Inventory')

@section('content')
<div class="main-content">
    <!-- Disable entire page scrolling for this view -->
    <style>
        html,
        body {
            overflow: hidden !important;
            height: 100% !important;
        }
    </style>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4 inventory-actions">
            <h1 class="h3 mb-0 text-gray-800">
                Medical Inventory
            </h1>

            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn-general btn-gray" data-bs-toggle="modal" data-bs-target="#reportPdfModal">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>

                <button type="button" class="btn-general btn-blue" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fas fa-plus-circle"></i> Add New Item
                </button>
                <button type="button" class="btn-general btn-blue" data-bs-toggle="modal" data-bs-target="#manageCategoryModal">
                    <i class="fas fa-tags"></i> Manage Categories
                </button>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row g-2 mb-3 filter-bar">
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search">
                                    <span style="font-size: 1rem; font-weight: 500; color: #6c757d;">
                                        <!-- Show full text on md and up -->
                                        <span class="d-none d-md-inline">Total items:</span>
                                        <!-- Show short text on sm and below -->
                                        <span class="d-inline d-md-none">Total:</span>

                                        @php
                                        $totalCount = 0;
                                        if (isset($items)) {
                                        if (is_object($items) && method_exists($items, 'total')) {
                                        $totalCount = $items->total();
                                        } elseif (is_countable($items)) {
                                        $totalCount = count($items);
                                        }
                                        }
                                        @endphp
                                        {{ $totalCount }}
                                    </span>

                                </i></span>
                            <input type="text" class="form-control" id="searchItem" placeholder="Search items, categories...">
                        </div>
                    </div>
                </div>

                {{-- rely on resources/css/inventory/index.css for table scrolling & sticky header --}}

                <div class="inventory-table-wrap flex-grow-1 overflow-auto h-100">
                    <table class="table table-bordered table-hover align-middle" id="inventoryTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th class="position-sticky text-white">
                                    <div class="dropdown header-dropdown">
                                        <button class="dropdown-toggle text-white fw-semibold border-0 bg-transparent p-0" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            Category <i class="fas fa-caret-down ms-1"></i>
                                        </button>
                                        <ul class="dropdown-menu shadow-sm" aria-labelledby="categoryDropdown" id="categoryDropdownMenu">
                                            <li><button class="dropdown-item category-filter-item" type="button" data-value="all">All</button></li>
                                            @foreach($categories as $cat)
                                            <li><button class="dropdown-item category-filter-item" type="button" data-value="{{ $cat->id }}">{{ $cat->name }}</button></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </th>
                                <th>Total Stock</th>
                                <th class="position-sticky text-white">
                                    <div class="dropdown header-dropdown">
                                        <button class="dropdown-toggle text-white fw-semibold border-0 bg-transparent p-0" type="button" id="stockDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            Status <i class="fas fa-caret-down ms-1"></i>
                                        </button>
                                        <ul class="dropdown-menu shadow-sm" aria-labelledby="stockDropdown" id="stockDropdownMenu">
                                            <li><button class="dropdown-item stock-filter-item" type="button" data-value="all">All</button></li>
                                            <li><button class="dropdown-item stock-filter-item" type="button" data-value="in">In Stock</button></li>
                                            <li><button class="dropdown-item stock-filter-item" type="button" data-value="low">Low Stock</button></li>
                                            <li><button class="dropdown-item stock-filter-item" type="button" data-value="out">Out of Stock</button></li>
                                        </ul>
                                    </div>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            @php
                            // Use the user-defined reminder value, default to 5 if not set
                            $threshold = $item->low_stock_reminder ?? 5;

                            $stockState = $item->total_stock <= 0
                                ? 'out'
                                : ($item->total_stock < $threshold ? 'low' : 'in' );
                                    @endphp
                                    <tr
                                    data-category="{{ optional($item->category)->id ?? '' }}"
                                    data-category-name="{{ strtolower(optional($item->category)->name ?? '') }}"
                                    data-stock="{{ $stockState }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ optional($item->category)->name }}</td>
                                    <td>{{ $item->total_stock }}</td>
                                    <td>
                                        @if($item->total_stock <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                            @elseif($item->total_stock < $threshold)
                                                <span class="badge bg-warning text-dark">Low Stock</span>
                                                @else
                                                <span class="badge bg-success">In Stock</span>
                                                @endif
                                    </td>
                                    <td>
                                        @include('inventory.partials.actions', ['item' => $item])
                                    </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No inventory items found.</td>
                                    </tr>
                                    @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS tweak: ensure dropdown rendered outside of overflowed parents is visible -->
<style>
    /* ensure header dropdown toggles sit above table visuals */
    .header-dropdown .dropdown-toggle {
        z-index: 1050;
        /* higher than typical card*/
    }

    /* ensure reparented dropdown-menus are on top */
    .inventory-dropdown-portal {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 3000;
        display: block;
    }

    /* Stronger highlight for header filters: visible but still subtle */
    .header-dropdown .dropdown-toggle {
        background: rgba(255, 255, 255, 0.16) !important;
        /* stronger highlight */
        border: 1px solid rgba(255, 255, 255, 0.24) !important;
        border-radius: 6px;
        padding-left: 0.35rem !important;
        padding-right: 0.35rem !important;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12) !important;
        transition: background .12s ease, transform .06s ease;
    }

    .header-dropdown .dropdown-toggle:hover,
    .header-dropdown .dropdown-toggle:focus {
        background: rgba(255, 255, 255, 0.26) !important;
        color: #fff !important;
        transform: translateY(-1px);
        text-decoration: none;
    }


    /* Make dropdown header buttons blend with table header */
    .header-dropdown .dropdown-toggle {
        color: #fff !important;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.1);
        border: none !important;
        box-shadow: none !important;
        padding: 0;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        cursor: pointer;
    }

    .header-dropdown .dropdown-toggle:hover,
    .header-dropdown .dropdown-toggle:focus {
        text-decoration: underline;
        color: #dbeafe !important;
    }

    /* Dropdown menu */
    .header-dropdown .dropdown-menu {
        min-width: 10rem;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        font-size: 0.9rem;
    }

    /* Dropdown items */
    .header-dropdown .dropdown-item {
        padding: 0.5rem 0.75rem;
        color: #333;
    }

    .header-dropdown .dropdown-item:hover {
        background-color: #f1f5f9;
        color: #111;
    }
</style>

<!-- JS: move dropdown menus to body on show, and back on hide. Use Popper via Bootstrap to position correctly. -->
<script>
    (function() {
        // helper to move a menu to body and keep a placeholder
        function portalizeDropdown(menuEl) {
            if (!menuEl) return null;
            // create wrapper that will be appended to body
            var wrapper = document.createElement('div');
            wrapper.className = 'inventory-dropdown-portal';
            wrapper.style.minWidth = menuEl.offsetWidth + 'px';
            wrapper.appendChild(menuEl);
            document.body.appendChild(wrapper);
            return wrapper;
        }

        function unportalizeDropdown(menuEl, originalParent) {
            if (!menuEl) return;
            // If the menu is currently inside a portal wrapper, remove wrapper and restore
            var wrapper = menuEl.parentNode;
            if (wrapper && wrapper.classList && wrapper.classList.contains('inventory-dropdown-portal')) {
                // move menu back first
                originalParent.appendChild(menuEl);
                // then remove wrapper
                try {
                    document.body.removeChild(wrapper);
                } catch (e) {
                    /* ignore */
                }
            } else {
                // simple append if wrapper wasn't present
                originalParent.appendChild(menuEl);
            }
        }

        // track original parents so we can restore
        var portalMap = new Map();

        // initialize dropdowns on the page
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownToggles = document.querySelectorAll('.header-dropdown .dropdown-toggle');
            dropdownToggles.forEach(function(toggle) {
                var menuId = toggle.getAttribute('aria-controls') || toggle.getAttribute('id') + 'Menu';
                // try to find the menu next to the toggle
                var menu = document.getElementById(menuId) || toggle.parentElement.querySelector('.dropdown-menu');
                if (!menu) return;

                // store original parent
                portalMap.set(menu, menu.parentElement);

                // Listen for bootstrap dropdown show/hide via events
                toggle.addEventListener('show.bs.dropdown', function(ev) {
                    // portalize
                    portalizeDropdown(menu);
                });
                toggle.addEventListener('hide.bs.dropdown', function(ev) {
                    // restore
                    var original = portalMap.get(menu);
                    if (original) unportalizeDropdown(menu, original);
                });

                // Additionally, ensure clicks on generated button items still close dropdown and apply filters
                menu.addEventListener('click', function(e) {
                    var btn = e.target.closest('button.dropdown-item');
                    if (!btn) return;
                    // close the dropdown programmatically
                    var bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                    if (bsDropdown) bsDropdown.hide();
                });
            });
        });
    })();
</script>
@include('inventory.partials.AddItem')
@include('inventory.partials.category', ['categories' => $categories])

@endsection
@push('modals')
@foreach($items as $item)
@include('inventory.edit')
@include('inventory.partials.RestockDeduct', ['item' => $item])
@include('inventory.partials.report_modal')
@endforeach
@endpush
@push('scripts')
@vite(['resources/js/inventory/index.js'])
<script>
    (function() {
        const table = document.getElementById('inventoryTable');
        const rows = table ? table.querySelectorAll('tbody tr') : [];
        const searchInput = document.getElementById('searchItem');
        let activeCategory = '';
        let activeStock = '';

        function applyFilters() {
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            rows.forEach(row => {
                const name = (row.children[1].textContent || '').toLowerCase();
                const categoryId = (row.dataset.category || '').toString();
                const categoryName = (row.dataset.categoryName || '').toLowerCase();
                const stock = (row.dataset.stock || '').toLowerCase();

                let visible = true;

                if (activeCategory && activeCategory !== '' && categoryId !== activeCategory) visible = false;
                if (activeStock && activeStock !== '' && stock !== activeStock) visible = false;
                if (query && !name.includes(query) && !categoryName.includes(query)) visible = false;

                row.style.display = visible ? '' : 'none';
            });
        }

        // Category filter clicks
        document.querySelectorAll('.category-filter-item').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const val = (this.dataset.value || '').toString().toLowerCase();
                activeCategory = (val === 'all' ? '' : val);
                applyFilters();
            });
        });

        // Stock filter clicks
        document.querySelectorAll('.stock-filter-item').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const val = (this.dataset.value || '').toString().toLowerCase();
                activeStock = (val === 'all' ? '' : val);
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                applyFilters();
            });
        }
    })();
</script>
@endpush