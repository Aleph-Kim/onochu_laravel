@extends('layouts.app')

@section('content')
    <div class="w-full max-w-[900px] mx-auto p-5 md:py-[30px] md:px-[60px] flex flex-col">
        <div class="flex items-center justify-between mb-5">
            <div class="flex flex-col">
                <h1 class="text-title-1 mb-1 text-label">{{ $songInfo['song']['title'] }}</h1>
                <div>
                    @foreach($songInfo['artists'] as $artist)
                        <a class="text-primary text-title-3 font-normal cursor-pointer transition-opacity hover:opacity-70"
                           href="{{ route('artist.detail', ['id' => $artist['flo_id']]) }}">
                            {{ $artist['name'] }}
                        </a>
                        @if(!$loop->last)
                            <span class="text-primary text-title-3">&</span>
                        @endif
                    @endforeach
                </div>
                <a class="text-subheadline text-label-secondary transition-colors hover:text-primary cursor-pointer mt-1"
                   href="{{ route('album.detail', ['id' => $songInfo['album']['flo_id']]) }}">{{ $songInfo['album']['title'] }}</a>
            </div>
            @if($songInfo['artists'][0]['img_url'])
                <div
                    class="w-[60px] h-[60px] rounded-full overflow-hidden flex-none shadow-artwork">
                    <a class="block transition-opacity hover:opacity-80"
                       href="{{ route('artist.detail', ['id' => $songInfo['artists'][0]['flo_id']]) }}">
                        <img src="{{ $songInfo['artists'][0]['img_url'] }}?/dims/resize/200x200/quality/90"
                             alt="Artist Profile" class="w-full h-full object-cover">
                    </a>
                </div>
            @endif
        </div>
        <div class="toggle-buttons hidden justify-center gap-0.5 w-fit mx-auto mb-[15px] bg-fill p-0.5 rounded-[9px]">
            <button
                class="toggle-btn cover-btn text-footnote py-1.5 px-5 rounded-[7px] font-semibold cursor-pointer transition-colors duration-200 bg-segment text-label shadow-knob"
                onclick="toggleView('cover')">커버보기
            </button>
            <button
                class="toggle-btn lyrics-btn text-footnote py-1.5 px-5 rounded-[7px] font-semibold cursor-pointer transition-colors duration-200 text-label-secondary"
                onclick="toggleView('lyrics')">가사보기
            </button>
        </div>
        <div class="album-container group w-full rounded-2xl overflow-hidden mb-5 relative max-w-[600px] mx-auto shadow-artwork">
            <img src="{{ $songInfo['album']['img_url'] }}?/dims/resize/500x500/quality/90" alt="Album Cover"
                 class="w-full h-auto block aspect-square">
            @if($songInfo['song']['lyrics'])
                <div
                    class="lyrics-overlay absolute top-0 left-0 w-full h-full bg-black/70 backdrop-blur-xl text-white text-callout flex items-start justify-center text-center p-5 opacity-0 transition-opacity duration-300 ease-in-out rounded-2xl whitespace-pre-wrap leading-[22px] min-[1023px]:group-hover:opacity-100 min-[1023px]:group-hover:overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar]:h-0 [&::-webkit-scrollbar-track]:bg-[#171717] [&::-webkit-scrollbar-track]:rounded-lg [&::-webkit-scrollbar-thumb]:bg-[#646464] [&::-webkit-scrollbar-thumb]:rounded-lg [&::-webkit-scrollbar-thumb:hover]:bg-[#ababab]">{{ $songInfo['song']['lyrics'] }}</div>
            @else
                <div
                    class="lyrics-overlay absolute top-0 left-0 w-full h-full bg-black/70 backdrop-blur-xl text-white text-callout flex items-center justify-center text-center p-5 opacity-0 transition-opacity duration-300 ease-in-out rounded-2xl whitespace-pre-wrap leading-[22px] min-[1023px]:group-hover:opacity-100 min-[1023px]:group-hover:overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar]:h-0 [&::-webkit-scrollbar-track]:bg-[#171717] [&::-webkit-scrollbar-track]:rounded-lg [&::-webkit-scrollbar-thumb]:bg-[#646464] [&::-webkit-scrollbar-thumb]:rounded-lg [&::-webkit-scrollbar-thumb:hover]:bg-[#ababab]">
                    가사가 제공되지 않는 곡입니다.
                </div>
            @endif
        </div>
        <div class="flex justify-end w-full mx-auto mb-5 max-w-[600px]">
            <a href="{{ route('music-app.open', ['id' => $songInfo['song']['flo_id']]) }}" target="_blank"
               class="inline-flex items-center justify-center min-h-[44px] px-6 rounded-full cursor-pointer text-white bg-primary text-headline transition-opacity hover:opacity-90 active:opacity-70">내 뮤직앱에서 재생하기</a>
        </div>
        <h3 class="text-title-3 text-label mt-8 mb-3">곡
            정보</h3>
        <div class="mb-[30px]">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 p-4 bg-surface rounded-xl">
                <div>
                    <p class="text-footnote text-label-secondary mb-0.5">발매일</p>
                    <p class="text-body text-label">{{ $songInfo['album']['release_date'] ?? '발매일 미상' }}</p>
                </div>
                <div>
                    <p class="text-footnote text-label-secondary mb-0.5">장르</p>
                    <p class="text-body text-label">{{ $songInfo['song']['genre'] }}</p>
                </div>
                @if($songInfo['song']['lyricist'])
                    <div>
                        <p class="text-footnote text-label-secondary mb-0.5">작사</p>
                        <p class="text-body text-label">{{ $songInfo['song']['lyricist'] }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-footnote text-label-secondary mb-0.5">작곡</p>
                    <p class="text-body text-label">{{ $songInfo['song']['composer'] ?: '작곡가 미상' }}</p>
                </div>
                @if($songInfo['song']['arranger'])
                    <div>
                        <p class="text-footnote text-label-secondary mb-0.5">편곡</p>
                        <p class="text-body text-label">{{ $songInfo['song']['arranger'] }}</p>
                    </div>
                @endif
            </div>
            <div class="flex justify-end mt-5 mb-[10px]">
                <a class="inline-flex items-center justify-center min-h-[44px] px-6 rounded-full cursor-pointer text-white bg-primary text-headline transition-opacity hover:opacity-90 active:opacity-70"
                   href="{{ route('recommends.create', ['id' => $songInfo['song']['flo_id']]) }}">추천하기</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/songDetail.js') }}"></script>
@endpush
