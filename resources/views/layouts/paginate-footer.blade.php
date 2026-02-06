
{{-- Global pagination footer: prefer child-defined section, else attempt fallback variables --}}
@if(View::hasSection('pagination-footer'))
<div class="pagination-footer">@yield('pagination-footer')</div>
@else
@php
// Try common paginator variable names passed from controllers
$possiblePaginators = [$pagination ?? null, $patients ?? null, $pendingPatients ?? null, $results ?? null];
$pager = null;
foreach ($possiblePaginators as $p) {
if ($p instanceof \Illuminate\Pagination\LengthAwarePaginator && $p->hasPages()) { $pager = $p; break; }
}
@endphp

@if(isset($pager) && $pager)
<div class="pagination-footer">
    {!! $pager->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') !!}
</div>
@endif
@endif