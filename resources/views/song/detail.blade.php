@extends('layouts.app')

@section('content')
<div class="w-full max-w-[900px] mx-auto p-5 md:py-[30px] md:px-[60px] flex flex-col">
    <div class="flex items-center justify-between mb-5">
        <div class="flex flex-col">
            <h1 class="text-2xl font-semibold mb-[10px]">{{ $songInfo['song']['title'] }}</h1>
            <div>
                @foreach($songInfo['artists'] as $artist)
                    <a class="text-[#777] mb-[5px] hover:text-primary cursor-pointer transition-colors" href="/artist/detail?id={{ $artist['flo_id'] }}">{{ $artist['name'] }}</a>
                @endforeach
            </div>
            <a class="text-[#999] transition hover:text-primary cursor-pointer" href="/album/detail?id={{ $songInfo['album']['flo_id'] }}">{{ $songInfo['album']['title'] }}</a>
        </div>
        @if($songInfo['artists'][0]['img_url'])
            <div class="w-[60px] h-[60px] rounded-full overflow-hidden flex-none">
                <a href="/artist/detail?id={{ $songInfo['artists'][0]['flo_id'] }}">
                    <img src="{{ $songInfo['artists'][0]['img_url'] }}?/dims/resize/200x200/quality/90" alt="Artist Profile" class="w-full h-full object-cover">
                </a>
            </div>
        @endif
    </div>
    <div class="toggle-buttons hidden justify-center rounded-[12px] overflow-hidden w-fit mx-auto mb-[15px]">
        <button class="toggle-btn cover-btn active text-sm py-2 px-5 bg-[#ccc] text-white cursor-pointer" onclick="toggleView('cover')">커버보기</button>
        <button class="toggle-btn lyrics-btn text-sm py-2 px-5 bg-[#ccc] text-white cursor-pointer" onclick="toggleView('lyrics')">가사보기</button>
    </div>
    <div class="album-container w-full rounded-lg overflow-hidden mb-[15px] relative max-w-[600px] mx-auto">
        <img src="{{ $songInfo['album']['img_url'] }}?/dims/resize/500x500/quality/90" alt="Album Cover" class="w-full h-auto block rounded-lg aspect-square">
        @if($songInfo['song']['lyrics'])
            <div class="lyrics-overlay">{{ $songInfo['song']['lyrics'] }}</div>
        @else
            <div class="lyrics-overlay none">가사가 제공되지 않는 곡입니다.</div>
        @endif
    </div>
    <div class="flex justify-end w-full gap-[10px] mx-auto mb-5 max-w-[600px]">
        <a href="{{ $songInfo['song']['url']['youtube'] }}" target="_blank" class="flex items-center justify-center p-[10px] rounded border cursor-pointer flex-none sm:flex-1 border-youtube text-youtube">YouTube Music</a>
        <a href="{{ $songInfo['song']['url']['flo'] }}" target="_blank" class="flex items-center justify-center p-[10px] rounded border cursor-pointer flex-none sm:flex-1 border-flo text-flo">FLO</a>
        <a href="{{ $songInfo['song']['url']['spotify'] }}" target="_blank" class="flex items-center justify-center p-[10px] rounded border cursor-pointer flex-none sm:flex-1 border-spotify text-spotify">Spotify</a>
    </div>
    <h3 class="text-primary font-medium mt-[30px] mb-[15px] text-xl">곡 정보</h3>
    <div class="mb-[30px]">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-[15px]">
            <div class="mb-[10px]">
                <p class="text-[1.2em] mb-[10px]">발매일</p>
                <p>{{ $songInfo['album']['release_date'] ?? '발매일 미상' }}</p>
            </div>
            <div class="mb-[10px]">
                <p class="text-[1.2em] mb-[10px]">장르</p>
                <p>{{ $songInfo['song']['genre'] }}</p>
            </div>
            @if($songInfo['song']['lyricist'])
                <div class="mb-[10px]">
                    <p class="text-[1.2em] mb-[10px]">작사</p>
                    <p>{{ $songInfo['song']['lyricist'] }}</p>
                </div>
            @endif
            <div class="mb-[10px]">
                <p class="text-[1.2em] mb-[10px]">작곡</p>
                <p>{{ $songInfo['song']['composer'] ?: '작곡가 미상' }}</p>
            </div>
            @if($songInfo['song']['arranger'])
                <div class="mb-[10px]">
                    <p class="text-[1.2em] mb-[10px]">편곡</p>
                    <p>{{ $songInfo['song']['arranger'] }}</p>
                </div>
            @endif
        </div>
        <div class="flex justify-end my-[10px]">
            <a class="inline-block py-[10px] px-[20px] rounded cursor-pointer transition-colors text-white bg-primary hover:bg-primary-light" href="/recommends?id={{ $songInfo['song']['flo_id'] }}">추천하기</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/songDetail.js') }}"></script>
@endpush
