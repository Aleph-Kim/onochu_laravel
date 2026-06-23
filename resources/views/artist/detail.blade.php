@extends('layouts.app')

@section('content')
<div class="pt-10 px-[30px] pb-[60px]">
    <div class="flex items-center mb-[60px] flex-wrap max-lg:flex-col max-lg:text-center max-lg:gap-5">
        <img src="{{ $artistInfo['img_url'] }}?/dims/resize/500x500/quality/90" class="w-[250px] h-[250px] rounded-full object-cover mr-5 max-lg:mr-0 max-sm:w-[200px] max-sm:h-[200px] shadow-[0_8px_32px_rgba(0,0,20,0.15)]">
        <div>
            <div class="text-[2.5em] font-bold tracking-tight text-[#111] max-sm:text-[2em]">{{ $artistInfo['name'] }}</div>
            <div class="mt-5 text-[#8b8b9a]">
                <span>{{ $artistInfo['group_type'] }}</span>
                <span class="between-bar"></span>
                <span>{{ $artistInfo['genre'] }}</span>
            </div>
        </div>
    </div>
    <div class="text-2xl font-bold tracking-tight text-[#111]">앨범 목록</div>
    <div class="my-5 mb-[35px] flex justify-evenly lg:justify-end items-center gap-5">
        <div class="custom-selector">
            <div class="selected-option">전체</div>
            <div class="options">
                <div class="option" data-type="all">전체</div>
                <div class="option" data-type="정규/미니">정규/미니</div>
                <div class="option" data-type="싱글">싱글</div>
                <div class="option" data-type="참여">참여</div>
            </div>
        </div>
        <div class="flex items-center gap-[10px]">
            <span class="toggle-label latest active">최신순</span>
            <label class="toggle-switch">
                <input type="checkbox" id="sortToggle">
                <span class="slider"></span>
            </label>
            <span class="toggle-label oldest">오래된순</span>
        </div>
        <div class="flex-1 max-w-[200px]">
            <input type="text" id="albumSearch" placeholder="앨범 검색"
                   class="w-full py-[8px] px-[14px] bg-[#f0f0f8] border-0 rounded-full text-sm transition-shadow focus:shadow-[0_0_0_2px_rgba(91,91,214,0.15)] focus:outline-none placeholder:text-[#b0b0c0]">
        </div>
    </div>
    <div class="albums grid grid-cols-[repeat(auto-fill,minmax(350px,1fr))] gap-4 min-h-[250px] max-sm:grid-cols-[repeat(auto-fill,minmax(140px,1fr))] max-sm:gap-3">
        @foreach($albumsInfo as $album)
            <div class="album flex flex-row items-start p-[14px] gap-[14px] rounded-2xl border border-[#ebebf0] bg-white transition-all hover:shadow-md hover:border-[#d8d8e8] hover:text-primary cursor-pointer max-sm:flex-col max-sm:gap-[5px]"
                 onclick="window.location.href = '/album/detail?id={{ $album['flo_id'] }}'">
                <img src="{{ $album['img_url'] }}?/dims/resize/350x350/quality/90" class="max-w-[200px] rounded-xl max-sm:w-full max-sm:h-auto max-sm:max-w-[150px] aspect-square object-cover">
                <div class="flex flex-col justify-center flex-1">
                    <div class="album-title font-bold text-[16px] mb-1 text-[#111]">{{ $album['title'] }}</div>
                    <div class="text-sm text-[#555]">{{ implode(' & ', array_column($album['artists'], 'name')) }}</div>
                    <div class="pt-[10px] max-sm:pt-0">
                        <div class="album-type text-[13px] text-[#8b8b9a] leading-[1.4]">{{ $album['type'] }}</div>
                        <div class="album-date text-[13px] text-[#8b8b9a] leading-[1.4]">{{ $album['release_date'] ?? '발매일 미상' }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/artistDetail.js') }}"></script>
@endpush
