@extends('layouts.app')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/artistDetail.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="profile">
        <img src="{{ $artistInfo['img_url'] }}?/dims/resize/500x500/quality/90">
        <div>
            <div class="profile-name">{{ $artistInfo['name'] }}</div>
            <div class="profile-info">
                <span>{{ $artistInfo['group_type'] }}</span>
                <span class="between-bar"></span>
                <span>{{ $artistInfo['genre'] }}</span>
            </div>
        </div>
    </div>
    <div class="albums-title">앨범 목록</div>
    <div class="albums-filter">
        <div class="custom-selector">
            <div class="selected-option">전체</div>
            <div class="options">
                <div class="option" data-type="all">전체</div>
                <div class="option" data-type="정규/미니">정규/미니</div>
                <div class="option" data-type="싱글">싱글</div>
                <div class="option" data-type="참여">참여</div>
            </div>
        </div>
        <div class="toggle-sort">
            <span class="toggle-label latest active">최신순</span>
            <label class="toggle-switch">
                <input type="checkbox" id="sortToggle">
                <span class="slider"></span>
            </label>
            <span class="toggle-label oldest">오래된순</span>
        </div>
        <div class="search-filter">
            <input type="text" id="albumSearch" placeholder="앨범 검색">
        </div>
    </div>
    <div class="albums">
        @foreach($albumsInfo as $album)
            <div class="album" onclick="window.location.href = '/album/detail?id={{ $album['flo_id'] }}'">
                <img src="{{ $album['img_url'] }}?/dims/resize/350x350/quality/90">
                <div class="album-info">
                    <div class="album-title">{{ $album['title'] }}</div>
                    <div class="album-artist">{{ implode(' & ', array_column($album['artists'], 'name')) }}</div>
                    <div class="album-type">{{ $album['type'] }}</div>
                    <div class="album-date">{{ $album['release_date'] ?? '발매일 미상' }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/artistDetail.js') }}"></script>
@endpush
