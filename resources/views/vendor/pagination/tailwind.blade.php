{{-- Portal pagination (overrides Laravel's default "pagination::tailwind" for every ->links()) --}}
@if ($paginator->hasPages())
<nav class="mv-pager" role="navigation" aria-label="Pagination">
    <p class="mv-pager-info">
        @if ($paginator->firstItem())
            Showing <b>{{ number_format($paginator->firstItem()) }}</b>–<b>{{ number_format($paginator->lastItem()) }}</b> of <b>{{ number_format($paginator->total()) }}</b>
        @else
            Showing {{ $paginator->count() }} of {{ number_format($paginator->total()) }}
        @endif
    </p>
    <div class="mv-pager-links">
        @if ($paginator->onFirstPage())
            <span class="mv-pg is-disabled" aria-disabled="true" aria-label="Previous page"><svg class="mv-i mv-i-sm"><use href="#i-chevron-left"/></svg></span>
        @else
            <a class="mv-pg" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"><svg class="mv-i mv-i-sm"><use href="#i-chevron-left"/></svg></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="mv-pg is-gap" aria-disabled="true">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="mv-pg is-on" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="mv-pg" href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="mv-pg" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"><svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></a>
        @else
            <span class="mv-pg is-disabled" aria-disabled="true" aria-label="Next page"><svg class="mv-i mv-i-sm"><use href="#i-chevron-right"/></svg></span>
        @endif
    </div>
</nav>
@endif
