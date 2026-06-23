@extends('layouts.app')

@section('content')
<div class="flex flex-col bg-white">
    <a class="profile-header relative h-[340px] overflow-hidden w-full rounded-b-[2rem]" @if(!empty($userInfo['profile_album_flo_id'])) href="/album/detail?id={{ $userInfo['profile_album_flo_id'] }}" @endif>
        <div class="profile-background absolute inset-0 bg-center bg-cover" style="background-image: url('{{ $userInfo['profile_img_url'] }}?size=1000x1000');"></div>
        <div class="max-w-[1200px] mx-auto px-6 h-full flex flex-col justify-end pb-8">
            @if($genreList)
                <p class="text-white/70 text-base font-medium z-[1]">{{ array_keys($genreList)[0] }} 장르를 좋아하는</p>
            @endif
            <p class="text-white text-[3.75rem] font-bold mb-1 z-[1] leading-tight">{{ $userInfo['nickname'] }} 님</p>
            <p class="text-white/70 text-base z-[1]">추천한 노래 {{ $userInfo['recommend_count'] }}개</p>
        </div>
    </a>

    @if($userInfo['recommend_count'] > 0)
        <div class="flex-grow bg-white">
            <div class="max-w-[1200px] mx-auto p-6 pb-0">
                <div class="flex gap-8 xl:flex-col">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold mb-6 tracking-tight text-[#111]">좋아하는 아티스트</h2>
                        <div class="flex flex-col gap-3 max-h-[400px] overflow-y-auto scrollbar-hide">
                            @foreach($artistList as $artist)
                                <a class="flex items-center p-4 rounded-2xl border border-[#ebebf0] transition-all hover:shadow-md hover:border-[#d8d8e8]" href="/artist/detail?id={{ $artist['flo_id'] }}">
                                    <img src="{{ $artist['img_url'] }}?/dims/resize/200x200/quality/90" alt="아티스트"
                                         class="w-16 h-16 rounded-full object-cover mr-4 shadow-sm">
                                    <div class="flex-grow">
                                        <h3 class="font-semibold text-lg text-[#111]">{{ $artist['name'] }}</h3>
                                        <p class="text-[#8b8b9a] text-sm">추천한 노래 {{ $artist['count'] }}개</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold mb-6 tracking-tight text-[#111]">좋아하는 장르</h2>
                        <div id="genreChart" class="w-full h-[400px]"></div>
                    </div>
                </div>
            </div>
            <div class="max-w-[1200px] mx-auto p-6 pb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold tracking-tight text-[#111]">추천하는 노래</h2>
                    <div class="flex justify-evenly lg:justify-end items-center gap-5">
                        <div class="flex items-center gap-[10px]">
                            <span class="toggle-label latest active">최신순</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="sortToggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label oldest">오래된순</span>
                        </div>
                        <div>
                            <input type="text" id="songSearch" placeholder="노래 검색"
                                   class="w-full py-[9px] px-[14px] bg-[#f0f0f8] border-0 rounded-full text-sm transition-shadow focus:shadow-[0_0_0_2px_rgba(91,91,214,0.15)] focus:outline-none placeholder:text-[#b0b0c0]">
                        </div>
                    </div>
                </div>
                <div class="songs-grid grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($songList as $song)
                        <div class="song-card flex items-center p-4 rounded-2xl border border-[#ebebf0] transition-all hover:shadow-md hover:border-[#d8d8e8] bg-white cursor-pointer">
                            <img src="{{ $song['album_img_url'] }}?/dims/resize/200x200/quality/90" alt="앨범커버"
                                 class="w-20 h-20 rounded-xl object-cover mr-4" loading="lazy"
                                 onclick="window.location.href='/recommends/detail?id={{ $song['id'] }}'">
                            <div class="flex-grow" onclick="window.location.href='/recommends/detail?id={{ $song['id'] }}'">
                                <h3 class="song-title font-semibold text-lg text-[#111]">{{ $song['song_title'] }}</h3>
                                <p class="text-[#8b8b9a] text-sm">{{ $song['artist_name'] }}</p>
                                <p class="recommend-date text-[#b0b0c0] text-xs mt-1">{{ date('Y.m.d', strtotime($song['recommend_date'])) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="flex-grow bg-white">
            <div class="max-w-[1200px] mx-auto px-6 py-20 flex flex-col items-center text-center">
                <svg viewBox="0 0 24 24" class="w-20 h-20 fill-[#d8d8e8] mb-6" aria-hidden="true">
                    <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
                </svg>
                <h2 class="text-2xl font-bold text-[#111] mb-2">아직 추천한 노래가 없어요</h2>
                <p class="text-[#8b8b9a]">이 사용자가 노래를 추천하면 이 곳에 표시됩니다.</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/lib/echarts.min.js') }}"></script>
@if($genreList)
    <script src="{{ asset('js/mypage.js') }}"></script>
    <script>
        const genreList = {!! json_encode($genreList) !!};
    </script>
@endif
@endpush
