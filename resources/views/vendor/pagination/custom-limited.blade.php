<style>
    .pagination .page-item.active .page-link {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
    }
</style>


@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, $current - 1);
        $end = min($last, $current + 1);
    @endphp

    <nav>
        <ul class="pagination justify-content-center">

            {{-- Previous --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }} mx-1">
                <a class="page-link rounded-pill" href="{{ $paginator->previousPageUrl() }}">Previous</a>
            </li>

            {{-- First Page --}}
            @if ($start > 1)
                <li class="page-item"><a class="page-link rounded-pill" href="{{ $paginator->url(1) }}">1</a></li>
                @if ($start > 2)
                    <li class="page-item disabled"><span class="page-link rounded-pill">…</span></li>
                @endif
            @endif

            {{-- Middle Pages --}}
            @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $i == $current ? 'active' : '' }}">
                    <a class="page-link rounded-pill" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            {{-- Last Page --}}
            @if ($end < $last)
                @if ($end < $last - 1)
                    <li class="page-item disabled"><span class="page-link rounded-pill">…</span></li>
                @endif
                <li class="page-item"><a class="page-link rounded-pill" href="{{ $paginator->url($last) }}">{{ $last }}</a></li>
            @endif

            {{-- Next --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }} mx-1">
                <a class="page-link rounded-pill" href="{{ $paginator->nextPageUrl() }}">Next</a>
            </li>

        </ul>
    </nav>
@endif
