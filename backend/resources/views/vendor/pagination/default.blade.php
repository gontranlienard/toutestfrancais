@if ($paginator->hasPages())
    <nav class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page disabled">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page">‹</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)

            {{-- Separator --}}
            @if (is_string($element))
                <span class="page disabled">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page">›</a>
        @else
            <span class="page disabled">›</span>
        @endif
    </nav>
@endif
