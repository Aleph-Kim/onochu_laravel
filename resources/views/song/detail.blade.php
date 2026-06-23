@extends('layouts.app')

@section('content')
<div class="w-full max-w-[900px] mx-auto p-5 md:py-[30px] md:px-[60px] flex flex-col">
    <div class="flex items-center justify-between mb-5">
        <div class="flex flex-col">
            <h1 class="text-2xl font-bold mb-[10px] tracking-tight text-[#111]">{{ $songInfo['song']['title'] }}</h1>
            <div>
                @foreach($songInfo['artists'] as $artist)
                    <a class="text-[#8b8b9a] mb-[5px] hover:text-primary cursor-pointer transition-colors font-medium" href="/artist/detail?id={{ $artist['flo_id'] }}">{{ $artist['name'] }}</a>
                @endforeach
            </div>
            <a class="text-[#b0b0c0] transition hover:text-primary cursor-pointer mt-1" href="/album/detail?id={{ $songInfo['album']['flo_id'] }}">{{ $songInfo['album']['title'] }}</a>
        </div>
        @if($songInfo['artists'][0]['img_url'])
            <div class="w-[60px] h-[60px] rounded-full overflow-hidden flex-none shadow-[0_4px_12px_rgba(0,0,20,0.12)]">
                <a href="/artist/detail?id={{ $songInfo['artists'][0]['flo_id'] }}">
                    <img src="{{ $songInfo['artists'][0]['img_url'] }}?/dims/resize/200x200/quality/90" alt="Artist Profile" class="w-full h-full object-cover">
                </a>
            </div>
        @endif
    </div>
    <div class="toggle-buttons hidden justify-center gap-1 w-fit mx-auto mb-[15px] bg-[#f0f0f8] p-1 rounded-full">
        <button class="toggle-btn cover-btn active text-sm py-1.5 px-5 rounded-full font-medium cursor-pointer" onclick="toggleView('cover')">커버보기</button>
        <button class="toggle-btn lyrics-btn text-sm py-1.5 px-5 rounded-full font-medium cursor-pointer" onclick="toggleView('lyrics')">가사보기</button>
    </div>
    <div class="album-container w-full rounded-2xl overflow-hidden mb-[15px] relative max-w-[600px] mx-auto">
        <img src="{{ $songInfo['album']['img_url'] }}?/dims/resize/500x500/quality/90" alt="Album Cover" class="w-full h-auto block aspect-square">
        @if($songInfo['song']['lyrics'])
            <div class="lyrics-overlay">{{ $songInfo['song']['lyrics'] }}</div>
        @else
            <div class="lyrics-overlay none">가사가 제공되지 않는 곡입니다.</div>
        @endif
    </div>
    <div class="flex justify-end w-full gap-[10px] mx-auto mb-5 max-w-[600px]">
        <a href="{{ $songInfo['song']['url']['youtube'] }}" target="_blank" class="flex items-center justify-center py-[10px] px-[16px] rounded-full border-2 cursor-pointer flex-none sm:flex-1 border-youtube text-youtube text-sm font-semibold hover:bg-youtube hover:text-white transition-colors">YouTube Music</a>
        <a href="{{ $songInfo['song']['url']['flo'] }}" target="_blank" class="flex items-center justify-center py-[10px] px-[16px] rounded-full border-2 cursor-pointer flex-none sm:flex-1 border-flo text-flo text-sm font-semibold hover:bg-flo hover:text-white transition-colors">FLO</a>
        <a href="{{ $songInfo['song']['url']['spotify'] }}" target="_blank" class="flex items-center justify-center py-[10px] px-[16px] rounded-full border-2 cursor-pointer flex-none sm:flex-1 border-spotify text-spotify text-sm font-semibold hover:bg-spotify hover:text-white transition-colors">Spotify</a>
    </div>
    <h3 class="text-[#333] font-bold mt-[30px] mb-[15px] text-lg tracking-tight border-b border-[#ebebf0] pb-3">곡 정보</h3>
    <div class="mb-[30px]">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-[15px]">
            <div class="mb-[10px]">
                <p class="text-sm font-medium text-[#8b8b9a] uppercase tracking-wider mb-2">발매일</p>
                <p class="text-[#333]">{{ $songInfo['album']['release_date'] ?? '발매일 미상' }}</p>
            </div>
            <div class="mb-[10px]">
                <p class="text-sm font-medium text-[#8b8b9a] uppercase tracking-wider mb-2">장르</p>
                <p class="text-[#333]">{{ $songInfo['song']['genre'] }}</p>
            </div>
            @if($songInfo['song']['lyricist'])
                <div class="mb-[10px]">
                    <p class="text-sm font-medium text-[#8b8b9a] uppercase tracking-wider mb-2">작사</p>
                    <p class="text-[#333]">{{ $songInfo['song']['lyricist'] }}</p>
                </div>
            @endif
            <div class="mb-[10px]">
                <p class="text-sm font-medium text-[#8b8b9a] uppercase tracking-wider mb-2">작곡</p>
                <p class="text-[#333]">{{ $songInfo['song']['composer'] ?: '작곡가 미상' }}</p>
            </div>
            @if($songInfo['song']['arranger'])
                <div class="mb-[10px]">
                    <p class="text-sm font-medium text-[#8b8b9a] uppercase tracking-wider mb-2">편곡</p>
                    <p class="text-[#333]">{{ $songInfo['song']['arranger'] }}</p>
                </div>
            @endif
        </div>
        <div class="flex justify-end my-[10px]">
            <a class="inline-block py-[10px] px-[24px] rounded-full cursor-pointer transition-all text-white bg-primary hover:bg-primary-light font-semibold shadow-sm hover:shadow-md" href="/recommends?id={{ $songInfo['song']['flo_id'] }}">추천하기</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/songDetail.js') }}"></script>
@endpush
