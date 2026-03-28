@extends('layouts.app')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
<div class="container">
    <a class="profile-header" href="/album/detail?id={{ $userInfo['profile_album_flo_id'] }}">
        <div class="profile-background" style="background-image: url('{{ $userInfo['profile_img_url'] }}?size=1000x1000');"></div>
        <div class="profile-info">
            @if($genreList)
                <p class="profile-genre">{{ array_keys($genreList)[0] }} 장르를 좋아하는</p>
            @endif
            <p class="profile-name">{{ $userInfo['nickname'] }} 님</p>
            <p class="profile-stats">추천한 노래 {{ $userInfo['recommend_count'] }}개</p>
        </div>
    </a>

    @if($userInfo['recommend_count'] > 0)
        <div class="mypage-content">
            <div class="stats-content">
                <div class="stats-grid">
                    <div class="stats-column">
                        <h2 class="section-title">좋아하는 아티스트</h2>
                        <div class="artist-list">
                            @foreach($artistList as $artist)
                                <a class="artist-card" href="/artist/detail?id={{ $artist['flo_id'] }}">
                                    <img src="{{ $artist['img_url'] }}?/dims/resize/200x200/quality/90" alt="아티스트" class="artist-image">
                                    <div class="artist-info">
                                        <h3 class="artist-name">{{ $artist['name'] }}</h3>
                                        <p class="artist-followers">추천한 노래 {{ $artist['count'] }}개</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="stats-column">
                        <h2 class="section-title">좋아하는 장르</h2>
                        <div id="genreChart" class="chart-container"></div>
                    </div>
                </div>
            </div>
            <div class="recommend-content">
                <h2 class="section-title">추천하는 노래</h2>
                <div class="songs-filter">
                    <div class="toggle-sort">
                        <span class="toggle-label latest active">최신순</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="sortToggle">
                            <span class="slider"></span>
                        </label>
                        <span class="toggle-label oldest">오래된순</span>
                    </div>
                    <div class="search-filter">
                        <input type="text" id="songSearch" placeholder="노래 검색">
                    </div>
                </div>
                <div class="songs-grid">
                    @foreach($songList as $song)
                        <div class="song-card">
                            <img src="{{ $song['album_img_url'] }}?/dims/resize/200x200/quality/90" alt="앨범커버" class="album-cover" loading="lazy" onclick="window.location.href='/recommends/detail?id={{ $song['id'] }}'">
                            <div class="song-info" onclick="window.location.href='/recommends/detail?id={{ $song['id'] }}'">
                                <h3 class="song-title">{{ $song['song_title'] }}</h3>
                                <p class="song-artist">{{ $song['artist_name'] }}</p>
                                <p class="recommend-date">{{ date('Y.m.d', strtotime($song['recommend_date'])) }}</p>
                            </div>
                            <div class="song-card-btn" onclick="setProfileAlbum('{{ $song['id'] }}')">
                                <button class="btn btn-submit">프로필 설정</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="skeleton-content">
            <div class="stats-content">
                <div class="stats-grid">
                    <div class="stats-column">
                        <div class="skeleton skeleton-section-title"></div>
                        <div class="artist-list">
                            <div class="skeleton skeleton-artist-card"></div>
                            <div class="skeleton skeleton-artist-card"></div>
                            <div class="skeleton skeleton-artist-card"></div>
                            <div class="skeleton skeleton-artist-card"></div>
                        </div>
                    </div>
                    <div class="stats-column">
                        <div class="skeleton skeleton-section-title"></div>
                        <div class="skeleton skeleton-chart"></div>
                    </div>
                </div>
            </div>
            <div class="skeleton-no-results">
                <p>추천한 노래가 없습니다.</p>
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
