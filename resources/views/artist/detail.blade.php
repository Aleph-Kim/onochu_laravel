@extends('layouts.app')

@section('content')
    <div class="pt-10 px-[30px] pb-[60px]">
        <div class="flex items-center mb-[60px] flex-wrap max-lg:flex-col max-lg:text-center max-lg:gap-5">
            <img src="{{ $artistInfo['img_url'] }}?/dims/resize/500x500/quality/90"
                 class="w-[250px] h-[250px] rounded-full object-cover mr-5 max-lg:mr-0 max-sm:w-[200px] max-sm:h-[200px] shadow-[0_8px_32px_rgba(0,0,20,0.15)]">
            <div>
                <div
                    class="text-[2.5em] font-bold tracking-tight text-[#111] max-sm:text-[2em]">{{ $artistInfo['name'] }}</div>
                <div class="mt-5 text-[#8b8b9a]">
                    <span>{{ $artistInfo['group_type'] }}</span>
                    <span class="inline-block w-px h-2.5 mx-1 my-0 bg-[#c8c8d8]"></span>
                    <span>{{ $artistInfo['genre'] }}</span>
                </div>
            </div>
        </div>
        <div class="text-2xl font-bold tracking-tight text-[#111]">앨범 목록</div>
        <div class="my-5 mb-[35px] flex justify-evenly lg:justify-end items-center gap-5">
            <div class="custom-selector relative w-[120px]" id="albumTypeFilter">
                <div
                    class="selected-option py-2 px-3.5 bg-bg border-[1.5px] border-[#ebebf0] rounded-full cursor-pointer text-[13px] font-medium text-center transition-[background,border-color] duration-200 hover:bg-[#ebebf0] hover:border-[#d5d5e0]">
                    전체
                </div>
                <div
                    class="options hidden absolute top-[calc(100%+4px)] left-0 w-full bg-white border-[1.5px] border-[#ebebf0] rounded-[14px] shadow-[0_8px_24px_rgba(0,0,0,0.08)] z-10 p-1 overflow-hidden">
                    <div
                        class="option py-2.5 px-4 text-sm cursor-pointer transition-[background] duration-[150ms] rounded-lg hover:bg-[#f0f0fa]"
                        data-type="all">전체
                    </div>
                    <div
                        class="option py-2.5 px-4 text-sm cursor-pointer transition-[background] duration-[150ms] rounded-lg hover:bg-[#f0f0fa]"
                        data-type="정규/미니">정규/미니
                    </div>
                    <div
                        class="option py-2.5 px-4 text-sm cursor-pointer transition-[background] duration-[150ms] rounded-lg hover:bg-[#f0f0fa]"
                        data-type="싱글">싱글
                    </div>
                    <div
                        class="option py-2.5 px-4 text-sm cursor-pointer transition-[background] duration-[150ms] rounded-lg hover:bg-[#f0f0fa]"
                        data-type="참여">참여
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-[10px]" id="albumSortToggle">
                <span
                    class="toggle-label latest text-sm text-primary font-semibold transition-colors duration-300">최신순</span>
                <label class="relative inline-block w-10 h-5">
                    <input type="checkbox" id="sortToggle" class="peer opacity-0 w-0 h-0">
                    <span
                        class="absolute cursor-pointer inset-0 bg-[#d8d8e4] transition-all duration-300 rounded-[10px] peer-checked:bg-primary before:content-[''] before:absolute before:h-4 before:w-4 before:left-0.5 before:bottom-0.5 before:bg-white before:transition-all before:duration-300 before:rounded-full before:shadow-[0_1px_3px_rgba(0,0,0,0.15)] peer-checked:before:translate-x-5"></span>
                </label>
                <span class="toggle-label oldest text-sm text-[#8b8b9a] transition-colors duration-300">오래된순</span>
            </div>
            <button id="albumSearchIconBtn" class="hidden max-sm:flex items-center justify-center w-9 h-9 shrink-0"
                    onclick="showAlbumSearch()">
                <svg viewBox="0 0 24 24" class="w-5 h-5 fill-[#b0b0c0]">
                    <g>
                        <path
                            d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path>
                    </g>
                </svg>
            </button>
            <div class="flex-1 max-w-[200px] flex items-center gap-2 max-sm:hidden" id="albumSearchWrap">
                <button id="albumSearchHideBtn" class="hidden w-[26px] h-[26px] shrink-0" onclick="hideAlbumSearch()">
                    <svg viewBox="0 0 24 24" class="fill-[#b0b0c0]">
                        <g>
                            <path
                                d="M21,11v1H5.64l6.72,6.72l-0.71,0.71L3.72,11.5l7.92-7.92l0.71,0.71L5.64,11H21z"></path>
                        </g>
                    </svg>
                </button>
                <input type="text" id="albumSearch" placeholder="앨범 검색"
                       class="w-full py-[8px] px-[14px] bg-[#f0f0f8] border-0 rounded-full text-sm transition-shadow focus:shadow-[0_0_0_2px_rgba(91,91,214,0.15)] focus:outline-none placeholder:text-[#b0b0c0]">
            </div>
        </div>
        <div
            class="albums grid grid-cols-[repeat(auto-fill,minmax(350px,1fr))] gap-4 min-h-[250px] max-sm:grid-cols-[repeat(auto-fill,minmax(140px,1fr))] max-sm:gap-3">
            @foreach($albumsInfo as $album)
                <div
                    class="album flex flex-row items-start p-[14px] gap-[14px] rounded-2xl border border-[#ebebf0] bg-white transition-all hover:shadow-md hover:border-[#d8d8e8] hover:text-primary cursor-pointer max-sm:flex-col max-sm:gap-[5px]"
                    onclick="window.location.href = '{{ route('album.detail', ['id' => $album['flo_id']]) }}'">
                    <img src="{{ $album['img_url'] }}?/dims/resize/350x350/quality/90"
                         class="max-w-[200px] rounded-xl max-sm:w-full max-sm:h-auto max-sm:max-w-[150px] aspect-square object-cover">
                    <div class="flex flex-col justify-center flex-1">
                        <div class="album-title font-bold text-[16px] mb-1 text-[#111]">{{ $album['title'] }}</div>
                        <div
                            class="text-sm text-[#555]">{{ implode(' & ', array_column($album['artists'], 'name')) }}</div>
                        <div class="pt-[10px] max-sm:pt-0">
                            <div class="album-type text-[13px] text-[#8b8b9a] leading-[1.4]">{{ $album['type'] }}</div>
                            <div
                                class="album-date text-[13px] text-[#8b8b9a] leading-[1.4]">{{ $album['release_date'] ?? '발매일 미상' }}</div>
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
