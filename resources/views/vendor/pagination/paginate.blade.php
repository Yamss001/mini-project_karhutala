@if ($paginator->hasPages())
    <nav class="pagination-container" aria-label="Pagination">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled">
                &laquo; Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="page-link">
                &laquo; Sebelumnya
            </a>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($elements as $element)

            @if (is_string($element))
                <span class="page-link disabled">
                    {{ $element }}
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)

                    @if ($page == $paginator->currentPage())
                        <span class="page-link active">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="page-link">
                            {{ $page }}
                        </a>
                    @endif

                @endforeach
            @endif

        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="page-link">
                Selanjutnya &raquo;
            </a>
        @else
            <span class="page-link disabled">
                Selanjutnya &raquo;
            </span>
        @endif

    </nav>
@endif
