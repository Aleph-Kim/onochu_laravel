@extends('layouts.app')

@section('content')
<div class="flex flex-col">
    <a class="profile-header relative h-[340px] overflow-hidden w-full" href="/album/detail?id={{ $userInfo['profile_album_flo_id'] }}">
        <div class="absolute inset-0 bg-center bg-cover" style="background-image: url('{{ $userInfo['profile_img_url'] }}?size=1000x1000');"></div>
        <div class="max-w-[1200px] mx-auto px-6 h-full flex flex-col justify-end pb-8">
            @if($genreList)
                <p class="text-[#e6e6e6] text-lg z-[1]">{{ array_keys($genreList)[0] }} 장르를 좋아하는</p>
            @endif
            <p class="text-white text-[3.75rem] font-bold mb-1 z-[1]">{{ $userInfo['nickname'] }} 님</p>
            <p class="text-[#e6e6e6] text-lg z-[1]">추천한 노래 {{ $userInfo['recommend_count'] }}개</p>
        </div>
    </a>

    @if($userInfo['recommend_count'] > 0)
        <div class="flex-grow bg-white">
            <div class="max-w-[1200px] mx-auto p-6 pb-0">
                <div class="flex gap-8 xl:flex-col">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold mb-6">좋아하는 아티스트</h2>
                        <div class="flex flex-col gap-4 max-h-[400px] overflow-y-auto scrollbar-hide">
                            @foreach($artistList as $artist)
                                <a class="flex items-center p-4 rounded-lg border border-[#e5e7eb] transition hover:shadow-md" href="/artist/detail?id={{ $artist['flo_id'] }}">
                                    <img src="{{ $artist['img_url'] }}?/dims/resize/200x200/quality/90" alt="아티스트"
                                         class="w-16 h-16 rounded-full object-cover mr-4">
                                    <div class="flex-grow">
                                        <h3 class="font-medium text-lg">{{ $artist['name'] }}</h3>
                                        <p class="text-[#6b7280] text-sm">추천한 노래 {{ $artist['count'] }}개</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold mb-6">좋아하는 장르</h2>
                        <div id="genreChart" class="w-full h-[400px]"></div>
                    </div>
                </div>
            </div>
            <div class="max-w-[1200px] mx-auto p-6 pb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">추천하는 노래</h2>
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
                                   class="w-full p-[10px] border border-[#ddd] rounded-[10px] text-sm transition focus:outline-none focus:border-primary">
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($songList as $song)
                        <div class="flex items-center p-4 rounded-lg border border-[#e5e7eb] transition hover:shadow-md bg-white cursor-pointer">
                            <img src="{{ $song['album_img_url'] }}?/dims/resize/200x200/quality/90" alt="앨범커버"
                                 class="w-20 h-20 rounded object-cover mr-4" loading="lazy"
                                 onclick="window.location.href='/recommends/detail?id={{ $song['id'] }}'">
                            <div class="flex-grow" onclick="window.location.href='/recommends/detail?id={{ $song['id'] }}'">
                                <h3 class="font-medium text-lg">{{ $song['song_title'] }}</h3>
                                <p class="text-[#6b7280]">{{ $song['artist_name'] }}</p>
                                <p class="text-[#6b7280] text-sm">{{ date('Y.m.d', strtotime($song['recommend_date'])) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="flex-grow bg-white relative">
            <div class="max-w-[1200px] mx-auto p-6 pb-0">
                <div class="flex gap-8 xl:flex-col">
                    <div class="flex-1">
                        <div class="skeleton skeleton-section-title"></div>
                        <div class="flex flex-col gap-4 max-h-[400px] overflow-y-auto scrollbar-hide">
                            <div class="skeleton skeleton-artist-card"></div>
                            <div class="skeleton skeleton-artist-card"></div>
                            <div class="skeleton skeleton-artist-card"></div>
                            <div class="skeleton skeleton-artist-card"></div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="skeleton skeleton-section-title"></div>
                        <div class="skeleton skeleton-chart"></div>
                    </div>
                </div>
            </div>
            <div class="skeleton-no-results">
                <p>활동이 없는 사용자입니다.</p>
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
