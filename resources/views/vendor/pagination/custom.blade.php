@if ($paginator->hasPages())
<nav aria-label="Page navigation">
    <ul class="pagination pagination-sm mb-0 justify-content-center">
        {{-- First Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-double-left"></i></span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url(1) }}" title="Halaman Pertama">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
            </li>
        @endif

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @php
            $start = max($paginator->currentPage() - 2, 1);
            $end = min($paginator->currentPage() + 2, $paginator->lastPage());
            
            // Adjust if we're near the beginning
            if ($paginator->currentPage() <= 3) {
                $end = min(5, $paginator->lastPage());
            }
            
            // Adjust if we're near the end
            if ($paginator->currentPage() > $paginator->lastPage() - 3) {
                $start = max($paginator->lastPage() - 4, 1);
            }
        @endphp

        {{-- Show first page if not in range --}}
        @if ($start > 1)
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
            </li>
            @if ($start > 2)
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            @endif
        @endif

        {{-- Page Numbers --}}
        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $paginator->currentPage())
                <li class="page-item active">
                    <span class="page-link">{{ $page }}</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endif
        @endfor

        {{-- Show last page if not in range --}}
        @if ($end < $paginator->lastPage())
            @if ($end < $paginator->lastPage() - 1)
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            @endif
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
            </li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
            </li>
        @endif

        {{-- Last Page Link --}}
        @if ($paginator->currentPage() == $paginator->lastPage())
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-double-right"></i></span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" title="Halaman Terakhir">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </li>
        @endif
    </ul>
</nav>
<div class="text-center mt-2">
    <small class="text-muted">
        Menampilkan {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} data
    </small>
</div>
@endif
