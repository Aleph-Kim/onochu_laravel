@extends('layouts.app')

@section('content')
    <div class="p-5 pb-[60px] md:pt-10 md:px-[30px]">
        <div class="flex items-center mb-10 flex-wrap max-lg:flex-col max-lg:items-start max-lg:gap-5">
            <img src="{{ $artistInfo['img_url'] }}?/dims/resize/500x500/quality/90"
                 class="w-[250px] h-[250px] rounded-full object-cover mr-6 max-lg:mr-0 max-sm:w-[200px] max-sm:h-[200px] shadow-artwork">
            <div>
                <div
                    class="text-large-title text-label max-sm:text-title-1">{{ $artistInfo['name'] }}</div>
                <div class="mt-2 text-subheadline text-label-secondary">
                    <span>{{ $artistInfo['group_type'] }}</span>
                    <span class="inline-block w-px h-2.5 mx-1 my-0 bg-label-tertiary"></span>
                    <span>{{ $artistInfo['genre'] }}</span>
                </div>
            </div>
        </div>
        <div class="text-title-2 text-label">앨범 목록</div>
        <div class="my-5 mb-[35px] flex justify-evenly lg:justify-end items-center gap-5">
            <div class="custom-selector relative w-[120px]" id="albumTypeFilter">
                <div
                    class="selected-option py-2 px-3.5 bg-fill rounded-full cursor-pointer text-subheadline font-medium text-primary text-center transition-colors duration-200 hover:bg-fill-strong">
                    전체
                </div>
                <div
                    class="options hidden absolute top-[calc(100%+6px)] left-0 w-full bg-raised border-[0.5px] border-separator rounded-[12px] shadow-popover z-10 p-1 overflow-hidden">
                    <div
                        class="option py-2.5 px-3 text-callout text-label cursor-pointer transition-colors duration-150 rounded-lg hover:bg-fill active:bg-fill-strong"
                        data-type="all">전체
                    </div>
                    <div
                        class="option py-2.5 px-3 text-callout text-label cursor-pointer transition-colors duration-150 rounded-lg hover:bg-fill active:bg-fill-strong"
                        data-type="정규/미니">정규/미니
                    </div>
                    <div
                        class="option py-2.5 px-3 text-callout text-label cursor-pointer transition-colors duration-150 rounded-lg hover:bg-fill active:bg-fill-strong"
                        data-type="싱글">싱글
                    </div>
                    <div
                        class="option py-2.5 px-3 text-callout text-label cursor-pointer transition-colors duration-150 rounded-lg hover:bg-fill active:bg-fill-strong"
                        data-type="참여">참여
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-[10px]" id="albumSortToggle">
                <span
                    class="toggle-label latest text-subheadline text-label font-semibold transition-colors duration-200">최신순</span>
                <label class="relative inline-block w-10 h-6 flex-none">
                    <input type="checkbox" id="sortToggle" class="peer opacity-0 w-0 h-0">
                    <span
                        class="absolute cursor-pointer inset-0 bg-track rounded-full before:content-[''] before:absolute before:h-5 before:w-5 before:left-0.5 before:top-0.5 before:bg-white before:transition-transform before:duration-200 before:rounded-full before:shadow-knob peer-checked:before:translate-x-4"></span>
                </label>
                <span class="toggle-label oldest text-subheadline text-label-secondary transition-colors duration-200">오래된순</span>
            </div>
            <button id="albumSearchIconBtn" class="hidden max-sm:flex items-center justify-center w-9 h-9 shrink-0"
                    onclick="showAlbumSearch()">
                <svg viewBox="0 0 24 24" class="w-5 h-5 fill-primary">
                    <g>
                        <path
                            d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path>
                    </g>
                </svg>
            </button>
            <div class="flex-1 max-w-[200px] flex items-center gap-2 max-sm:hidden" id="albumSearchWrap">
                <button id="albumSearchHideBtn" class="hidden w-[26px] h-[26px] shrink-0" onclick="hideAlbumSearch()">
                    <svg viewBox="0 0 24 24" class="fill-primary">
                        <g>
                            <path
                                d="M21,11v1H5.64l6.72,6.72l-0.71,0.71L3.72,11.5l7.92-7.92l0.71,0.71L5.64,11H21z"></path>
                        </g>
                    </svg>
                </button>
                <input type="text" id="albumSearch" placeholder="앨범 검색"
                       class="w-full h-9 px-3 bg-fill border-0 rounded-[10px] text-callout text-label transition-shadow focus:shadow-[0_0_0_3px_rgb(var(--tint)/0.35)] focus:outline-none placeholder:text-label-secondary">
            </div>
        </div>
        <div
            class="albums grid grid-cols-[repeat(auto-fill,minmax(350px,1fr))] gap-4 min-h-[250px] max-sm:grid-cols-[repeat(auto-fill,minmax(140px,1fr))] max-sm:gap-3">
            @foreach($albumsInfo as $album)
                <div
                    class="album flex flex-row items-start p-3 gap-[14px] rounded-2xl bg-surface transition-opacity hover:opacity-80 active:opacity-60 cursor-pointer max-sm:flex-col max-sm:gap-2"
                    onclick="window.location.href = '{{ route('album.detail', ['id' => $album['flo_id']]) }}'">
                    <img src="{{ $album['img_url'] }}?/dims/resize/350x350/quality/90"
                         class="max-w-[200px] rounded-lg shadow-artwork max-sm:w-full max-sm:h-auto max-sm:max-w-[150px] aspect-square object-cover">
                    <div class="flex flex-col justify-center flex-1 min-w-0 max-sm:justify-start">
                        <div class="album-title text-headline mb-0.5 text-label">{{ $album['title'] }}</div>
                        <div class="text-subheadline text-label-secondary line-clamp-2">
                            @foreach($album['artists'] as $artist)
                                <span class="cursor-pointer hover:text-primary transition-colors"
                                      onclick="event.stopPropagation(); window.location.href = '{{ route('artist.detail', ['id' => $artist['flo_id']]) }}'">{{ $artist['name'] }}</span>
                                @if(!$loop->last)
                                    <span>&</span>
                                @endif
                            @endforeach
                        </div>
                        <div class="pt-[10px] max-sm:pt-0">
                            <div class="album-type text-footnote text-label-secondary">{{ $album['type'] }}</div>
                            <div
                                class="album-date text-footnote text-label-secondary tabular-nums">{{ $album['release_date'] ?? '발매일 미상' }}</div>
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
