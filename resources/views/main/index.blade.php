@extends('layouts.app')

@section('content')
@php
    $newAlbumSectionTitle = session('user') ? session('user.nickname') . '님이 추천한 아티스트의 신규 앨범' : '추천 많은 아티스트의 신규 앨범';
    $artistSectionTitle = session('user') ? session('user.nickname') . '님이 추천한 아티스트' : '추천 많은 아티스트';
@endphp

<div class="slide-container">
    @if(!empty($recommends))
        <div class="music-slider">
            @foreach($recommends as $recommend)
                <a class="music-slide" href="/recommends/detail?id={{ $recommend['id'] }}">
                    <div class="music-card-wrap">
                        <div class="music-card-box">
                            <div class="music-card-body">
                                <span class="music-card-artist-img">
                                    <img src="{{ $recommend['artist_img_url'] }}?size=350x350" loading="lazy">
                                </span>
                                <div class="music-card-bg" style="background-image: url({{ $recommend['album_img_url'] }}?size=500x500);"></div>
                            </div>
                            <div class="music-card-footer">
                                <div>
                                    <span class="music-card-title">{{ $recommend['song_title'] }}</span>
                                    <span class="music-card-artist-name">{{ $recommend['artist_name'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="skeleton-container" data-text="다른 유저들의 추천 노래를 이 곳에서 보여드릴게요!">
            <div class="skeleton-scroll">
                @for($i = 0; $i < 10; $i++)
                    <div class="skeleton-slide">
                        <div class="rounded-2xl overflow-hidden shadow-[0_8px_24px_rgba(0,0,40,0.12)] bg-white">
                            <div class="skeleton skeleton-card"></div>
                            <div class="py-[14px] px-[20px] bg-white">
                                <div class="skeleton skeleton-title"></div>
                                <div class="skeleton skeleton-artist"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endif
</div>

<div class="md:w-[70%] md:max-w-[1200px] md:mx-auto md:px-[15px]">
    <div class="mt-[30px]">
        <h2 class="text-2xl font-bold mb-6 tracking-tight">{{ $newAlbumSectionTitle }}</h2>
        @if(!empty($newAlbums))
            <div class="flex gap-4 py-4 overflow-x-auto scrollbar-hide">
                @foreach($newAlbums as $newAlbum)
                    @php $artistName = implode(' & ', array_column($newAlbum['artists'], 'name')); @endphp
                    <a class="w-[200px] flex-none rounded-2xl shadow-sm border border-[#ebebf0] bg-white overflow-hidden hover:shadow-md transition-shadow" href="/album/detail?id={{ $newAlbum['flo_id'] }}&new_album=true">
                        <img src="{{ $newAlbum['img_url'] }}?size=350x350" loading="lazy" class="w-full" />
                        <div class="w-[200px] p-3">
                            <h3 class="font-semibold truncate text-[#111]">{{ $newAlbum['title'] }}</h3>
                            <p class="truncate text-sm text-[#8b8b9a]">{{ $artistName }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="skeleton-container" data-text="새로 나온 앨범이 있다면 이 곳에서 알려드릴게요!">
                <div class="skeleton-scroll">
                    @for($i = 0; $i < 10; $i++)
                        <div class="rounded-2xl shadow-sm border border-[#ebebf0] bg-white overflow-hidden">
                            <div class="skeleton w-48 h-48"></div>
                            <div class="skeleton-content">
                                <div class="skeleton skeleton-title"></div>
                                <div class="skeleton skeleton-artist"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>

    <div class="mt-[40px]">
        <h2 class="text-2xl font-bold mb-6 tracking-tight">{{ $artistSectionTitle }}</h2>
        @if(!empty($artists))
            <div class="flex gap-4 py-4 overflow-x-auto scrollbar-hide">
                @foreach($artists as $artist)
                    <a class="flex-none basis-48" href="/artist/detail?id={{ $artist['flo_id'] }}">
                        <img src="{{ $artist['img_url'] }}?size=350x350" loading="lazy" class="aspect-square rounded-full w-full h-auto object-cover shadow-[0_4px_16px_rgba(0,0,20,0.12)]" />
                        <div class="p-2 text-center">
                            <p class="font-semibold line-clamp-2 text-[#111]">{{ $artist['name'] }}</p>
                            <div class="flex items-center justify-center gap-1 mt-1">
                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-[#5b5bd6]"><g class="style-scope yt-icon"><path d="M18.77,11h-4.23l1.52-4.94C16.38,5.03,15.54,4,14.38,4c-0.58,0-1.14,0.24-1.52,0.65L7,11H3v10h4h1h9.43 c1.06,0,1.98-0.67,2.19-1.61l1.34-6C21.23,12.15,20.18,11,18.77,11z M7,20H4v-8h3V20z M19.98,13.17l-1.34,6 C18.54,19.65,18.03,20,17.43,20H8v-8.61l5.6-6.06C13.79,5.12,14.08,5,14.38,5c0.26,0,0.5,0.11,0.63,0.3 c0.07,0.1,0.15,0.26,0.09,0.47l-1.52,4.94L13.18,12h1.35h4.23c0.41,0,0.8,0.17,1.03,0.46C19.92,12.61,20.05,12.86,19.98,13.17z" class="style-scope yt-icon"></path></g></svg>
                                <span class="text-sm text-[#8b8b9a]">{{ $artist['recommend_cnt'] }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="skeleton-container" data-text="아직 추천 아티스트가 없네요!&#10;나만의 노래를 추천해주세요!">
                <div class="skeleton-scroll">
                    @for($i = 0; $i < 10; $i++)
                        <div class="skeleton-artist-card">
                            <div class="skeleton skeleton-image"></div>
                            <div class="skeleton-content">
                                <div class="skeleton skeleton-name"></div>
                                <div class="skeleton skeleton-count"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/lib/flickity.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>
@endpush
