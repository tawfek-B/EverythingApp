<style>
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
        margin: 0;
        margin-bottom: 50px;
        list-style: none;
    }

    .page-link {
        padding: 4px 6px;
        font-size: 24px;
        border-radius: 7.5px;
        font-weight: bold;
        color: #6699CC;
        background-color: transparent;
        border: none;
        text-decoration: none;
        transition: color 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .page-link:hover {
        border-radius: 7.5px;
        color: rgb(0, 0, 0);
        background-color: #6699CC;
        box-shadow: 0 0 15px #EBEDF2;
    }

    .page-item.active .page-link {
        background-color: #6699CC;
        color: black;
        border: none;
        box-shadow: 0 0 10px #EBEDF2;
    }

    .page-item.disabled .page-link {
        color: #555555;
        border-color: #555555;
        pointer-events: none;
    }

    .page-item {
        margin: 0 5px;
    }

    .arrow-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #EBEDF2;
        color: #6699CC;
        border: 2px solid #6699CC;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 18px;
        font-weight: bold;
        text-decoration: none;
        margin: 0 10px;
    }
</style>

@if ($paginator->hasPages())
    {{-- <div class="pagination-info" style="text-align: center; margin-bottom: 20px; font-size: 24px; color: #000000;">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} items
    </div> --}}

    <nav>
        <ul class="pagination">
            @if (!$paginator->onFirstPage())
                <li class="page-item">
                    <a class="page-link arrow-button" href="{{ $paginator->url(1) }}" aria-label="First">&laquo;</a>
                </li>
                <li class="page-item">
                    <a class="page-link arrow-button" style="width:50px; height:50px" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        aria-label="Previous">&lsaquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link arrow-button" aria-hidden="true">&laquo;</span>
                </li>
                <li class="page-item disabled">
                    <span class="page-link arrow-button"  style="width:50px; height:50px"aria-hidden="true">&lsaquo;</span>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link"
                                    href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link arrow-button" style="width:50px; height:50px" href="{{ $paginator->nextPageUrl() }}" rel="next"
                        aria-label="Next">&rsaquo;</a>
                </li>
                <li class="page-item">
                    <a class="page-link arrow-button" href="{{ $paginator->url($paginator->lastPage()) }}"
                        aria-label="Last">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link arrow-button" style="width:50px; height:50px" aria-hidden="true">&rsaquo;</span>
                </li>
                <li class="page-item disabled">
                    <span class="page-link arrow-button" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
