@extends('layouts.app')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/songDetail.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="song-header">
        <div class="song-header-info">
            <h1 class="song-title">{{ $songInfo['song']['title'] }}</h1>
            <div>
                @foreach($songInfo['artists'] as $artist)
                    <a class="song-artist" href="/artist/detail?id={{ $artist['flo_id'] }}">{{ $artist['name'] }}</a>
                @endforeach
            </div>
            <a class="song-album" href="/album/detail?id={{ $songInfo['album']['flo_id'] }}">{{ $songInfo['album']['title'] }}</a>
        </div>
        @if($songInfo['artists'][0]['img_url'])
            <div class="artist-profile">
                <a href="/artist/detail?id={{ $songInfo['artists'][0]['flo_id'] }}">
                    <img src="{{ $songInfo['artists'][0]['img_url'] }}?/dims/resize/200x200/quality/90" alt="Artist Profile">
                </a>
            </div>
        @endif
    </div>
    <div class="toggle-buttons">
        <button class="toggle-btn cover-btn active" onclick="toggleView('cover')">커버보기</button>
        <button class="toggle-btn lyrics-btn" onclick="toggleView('lyrics')">가사보기</button>
    </div>
    <div class="album-container">
        <img src="{{ $songInfo['album']['img_url'] }}?/dims/resize/500x500/quality/90" alt="Album Cover" class="album-image">
        @if($songInfo['song']['lyrics'])
            <div class="lyrics-overlay">{{ $songInfo['song']['lyrics'] }}</div>
        @else
            <div class="lyrics-overlay none">가사가 제공되지 않는 곡입니다.</div>
        @endif
    </div>
    <div class="music-platforms">
        <a href="{{ $songInfo['song']['url']['youtube'] }}" target="_blank" class="platform-btn platform-youtube">YouTube Music</a>
        <a href="{{ $songInfo['song']['url']['flo'] }}" target="_blank" class="platform-btn platform-flo">FLO</a>
        <a href="{{ $songInfo['song']['url']['spotify'] }}" target="_blank" class="platform-btn platform-spotify">Spotify</a>
    </div>
    <h3 class="section-title">곡 정보</h3>
    <div class="song-details">
        <div class="details-grid">
            <div class="details-item">
                <p class="details-label">발매일</p>
                <p class="details-value">{{ $songInfo['album']['release_date'] ?? '발매일 미상' }}</p>
            </div>
            <div class="details-item">
                <p class="details-label">장르</p>
                <p class="details-value">{{ $songInfo['song']['genre'] }}</p>
            </div>
            @if($songInfo['song']['lyricist'])
                <div class="details-item">
                    <p class="details-label">작사</p>
                    <p class="details-value">{{ $songInfo['song']['lyricist'] }}</p>
                </div>
            @endif
            <div class="details-item">
                <p class="details-label">작곡</p>
                <p class="details-value">{{ $songInfo['song']['composer'] ?: '작곡가 미상' }}</p>
            </div>
            @if($songInfo['song']['arranger'])
                <div class="details-item">
                    <p class="details-label">편곡</p>
                    <p class="details-value">{{ $songInfo['song']['arranger'] }}</p>
                </div>
            @endif
        </div>
        <div class="buttons">
            <a class="btn-recommends" href="/recommends?id={{ $songInfo['song']['flo_id'] }}">추천하기</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/songDetail.js') }}"></script>
@endpush
