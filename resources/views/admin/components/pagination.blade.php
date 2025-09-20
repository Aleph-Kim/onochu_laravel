@php
    $totalPages = $paginator->lastPage();
    $currentPage = $paginator->currentPage();

    // 최대 표시 페이지 수
    $maxButtons = 7;

    // 시작 페이지 번호 계산 (현재 페이지 중심으로)
    $start = max($currentPage - floor($maxButtons / 2), 1);
    // 끝 페이지 번호 계산
    $end = $start + $maxButtons - 1;

    // 끝 페이지가 전체 페이지 수보다 크면 조정
    if ($end > $totalPages) {
        $end = $totalPages;
        // 시작 페이지도 재조정
        $start = max($end - $maxButtons + 1, 1);
    }
@endphp
<div class="pagination col-group">
    @if($currentPage > 1)
        <a href="{{ $paginator->previousPageUrl() }}" class="page-prev-btn">
            <i class="xi-angle-left-min"></i>
        </a>
    @endif

    @for($page = $start; $page <= $end; $page++)
        <a href="{{ $paginator->url($page) }}" class="page-btn {{ $currentPage == $page ? 'active' : '' }}">
            {{ $page }}
        </a>
    @endfor

    @if($currentPage < $totalPages)
        <a href="{{ $paginator->nextPageUrl() }}" class="page-nav-btn">
            <i class="xi-angle-right-min"></i>
        </a>
    @endif
</div>
