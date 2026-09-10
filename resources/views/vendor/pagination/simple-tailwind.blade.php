{{-- Portal pagination for simple paginators (overrides "pagination::simple-tailwind") --}}
@if ($paginator->hasPages())
<nav class="mv-pager" role="navigation" aria-label="Pagination">
    <p class="mv-pager-info">Page <b>{{ $paginator->currentPage() }}</b></p>
    <div class="mv-pager-links">
        @if ($paginator->onFirstPage())
            <span class="mv-pg is-disabled" aria-disabled="true"><svg class="mv-i mv-i-sm"><use href="#i-chevron-left"/></svg>Previous</span>
        @else
            <a class="mv-pg" href="{{ $paginator->previousPageUrl() }}" rel="prev"><svg class="mv-i mv-i-sm"><use href="#i-chevron-left"/></svg>Previous</a>
        @endif
        @if ($paginator->hasMorePages())
            <a class="mv-pg" href="{{ $paginator->nextPageUrl() }}" rel="next">Next<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></a>
        @else
            <span class="mv-pg is-disabled" aria-disabled="true">Next<svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></span>
        @endif
    </div>
</nav>
@endif
