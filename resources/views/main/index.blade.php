@extends('layouts.app')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
@endpush

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
                        <div class="skeleton skeleton-card"></div>
                        <div class="skeleton-footer">
                            <div class="skeleton skeleton-title"></div>
                            <div class="skeleton skeleton-artist"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endif
</div>

<div class="main-container">
    <div class="new-album-section">
        <h2>{{ $newAlbumSectionTitle }}</h2>
        @if(!empty($newAlbums))
            <div class="new-album-container">
                @foreach($newAlbums as $newAlbum)
                    @php $artistName = implode(' & ', array_column($newAlbum['artists'], 'name')); @endphp
                    <a class="album-card" href="/album/detail?id={{ $newAlbum['flo_id'] }}&new_album=true">
                        <img src="{{ $newAlbum['img_url'] }}?size=350x350" loading="lazy" />
                        <div class="album-card-content">
                            <h3 class="album-card-title">{{ $newAlbum['title'] }}</h3>
                            <p class="album-card-artist">{{ $artistName }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="skeleton-container" data-text="새로 나온 앨범이 있다면 이 곳에서 알려드릴게요!">
                <div class="skeleton-scroll">
                    @for($i = 0; $i < 10; $i++)
                        <div class="skeleton-album-card">
                            <div class="skeleton skeleton-image"></div>
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

    <div class="artist-section">
        <h2>{{ $artistSectionTitle }}</h2>
        @if(!empty($artists))
            <div class="artist-container">
                @foreach($artists as $artist)
                    <a class="artist-card" href="/artist/detail?id={{ $artist['flo_id'] }}">
                        <img src="{{ $artist['img_url'] }}?size=350x350" loading="lazy" />
                        <div class="artist-card-content">
                            <p class="artist-card-name">{{ $artist['name'] }}</p>
                            <div class="artist-card-recommend-count">
                                <svg viewBox="0 0 24 24"><g class="style-scope yt-icon"><path d="M18.77,11h-4.23l1.52-4.94C16.38,5.03,15.54,4,14.38,4c-0.58,0-1.14,0.24-1.52,0.65L7,11H3v10h4h1h9.43 c1.06,0,1.98-0.67,2.19-1.61l1.34-6C21.23,12.15,20.18,11,18.77,11z M7,20H4v-8h3V20z M19.98,13.17l-1.34,6 C18.54,19.65,18.03,20,17.43,20H8v-8.61l5.6-6.06C13.79,5.12,14.08,5,14.38,5c0.26,0,0.5,0.11,0.63,0.3 c0.07,0.1,0.15,0.26,0.09,0.47l-1.52,4.94L13.18,12h1.35h4.23c0.41,0,0.8,0.17,1.03,0.46C19.92,12.61,20.05,12.86,19.98,13.17z" class="style-scope yt-icon"></path></g></svg>
                                <span>{{ $artist['recommend_cnt'] }}</span>
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
