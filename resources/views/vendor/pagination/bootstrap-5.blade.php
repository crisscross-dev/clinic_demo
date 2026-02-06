@if ($paginator->hasPages())
<nav class="d-flex justify-content-between align-items-center paging-control">

    {{-- Mobile View --}}
    <div class="d-sm-none mb-0 d-flex justify-content-between w-100">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
        <span class="bt-5-btn-toggle bt-5-btn-toggle-blue disabled">&laquo; Prev</span>
        @else
        <a class="bt-5-btn-toggle bt-5-btn-toggle-blue" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Prev</a>
        @endif

        {{-- Page Info --}}
        <span class="bt-5-btn-toggle bt-5-btn-toggle-blue disabled">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </span>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
        <a class="bt-5-btn-toggle bt-5-btn-toggle-blue" href="{{ $paginator->nextPageUrl() }}" rel="next">Next &raquo;</a>
        @else
        <span class="bt-5-btn-toggle bt-5-btn-toggle-blue disabled">Next &raquo;</span>
        @endif
    </div>

    {{-- Desktop View --}}
    <div class="d-none d-sm-flex align-items-center w-100 justify-content-between">
        <small class="text-muted">
            <strong>Page {{ $paginator->currentPage() }}</strong> of
            <strong>{{ $paginator->lastPage() }}</strong>
        </small>

        <div class="d-flex gap-1 align-items-center">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
            <span class="bt-5-btn-toggle bt-5-btn-toggle-blue disabled" title="Previous Page">&lsaquo;</span>
            @else
            <a class="bt-5-btn-toggle bt-5-btn-toggle-blue" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Previous Page">&lsaquo;</a>
            @endif

            @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
            @endphp

            {{-- Always show first page --}}
            @if ($start > 1)
            <a class="bt-5-btn-toggle bt-5-btn-toggle-blue {{ $current == 1 ? 'active' : '' }}" href="{{ $paginator->url(1) }}">1</a>
            @if ($start > 2)
            <a class="bt-5-btn-toggle bt-5-btn-toggle-blue" href="{{ $paginator->url($start - 1) }}">...</a>
            @endif
            @endif

            {{-- Pages around current --}}
            @for ($i = $start; $i <= $end; $i++)
                <a class="bt-5-btn-toggle bt-5-btn-toggle-blue {{ $i == $current ? 'active' : '' }}" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                @endfor

                {{-- Always show last page --}}
                @if ($end < $last)
                    @if ($end < $last - 1)
                    <a class="bt-5-btn-toggle bt-5-btn-toggle-blue" href="{{ $paginator->url($end + 1) }}">...</a>
                    @endif
                    <a class="bt-5-btn-toggle bt-5-btn-toggle-blue {{ $current == $last ? 'active' : '' }}" href="{{ $paginator->url($last) }}">{{ $last }}</a>
                    @endif

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                    <a class="bt-5-btn-toggle bt-5-btn-toggle-blue" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Next Page">&rsaquo;</a>
                    @else
                    <span class="bt-5-btn-toggle bt-5-btn-toggle-blue disabled" title="Next Page">&rsaquo;</span>
                    @endif

                    {{-- Jump to Page --}}
                    <form action="" method="GET" class="d-flex align-items-center ms-2" style="gap: 4px;">
                        <input type="number" name="page" min="1" max="{{ $paginator->lastPage() }}"
                            class="form-control form-control-sm" style="width: 70px;" placeholder="Page">
                        <button type="submit" class="btn btn-xsm btn-blue">Go</button>
                    </form>
        </div>
    </div>
</nav>
@endif