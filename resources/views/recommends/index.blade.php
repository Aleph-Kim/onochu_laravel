@extends('layouts.app')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/recommends.css') }}">
@endpush

@section('content')
<div class="container">
    <form class="recommends-form" action="/recommends/post" method="post">
        @csrf
        <div class="artist-info">
            <img src="{{ $songInfo['artists'][0]['img_url'] }}?/dims/resize/200x200/quality/90" onclick="window.location.href = '/artist/detail?id={{ $songInfo['artists'][0]['flo_id'] }}'">
            <span>
                @foreach($songInfo['artists'] as $artist)
                    <span class="artist-name" onclick="window.location.href = '/artist/detail?id={{ $artist['flo_id'] }}'">{{ $artist['name'] }}</span>
                @endforeach
            </span>
        </div>
        <div class="song-img">
            <img src="{{ $songInfo['album']['img_url'] }}?/dims/resize/350x350/quality/90">
        </div>
        <div class="song-info">
            <h2>{{ $songInfo['song']['title'] }}</h2>
            <p>
                {{ $songInfo['album']['release_date'] ?? '발매일 미상' }}
                <span class="between-bar"></span>
                {{ $songInfo['song']['genre'] }}
                <span class="between-bar"></span>
                {{ $songInfo['song']['play_time'] }}
            </p>
        </div>
        <div class="recommends-rating">
            <input type="radio" id="star5" name="score" value="5">
            <label for="star5">★</label>
            <input type="radio" id="star4" name="score" value="4">
            <label for="star4">★</label>
            <input type="radio" id="star3" name="score" value="3" checked>
            <label for="star3">★</label>
            <input type="radio" id="star2" name="score" value="2">
            <label for="star2">★</label>
            <input type="radio" id="star1" name="score" value="1">
            <label for="star1">★</label>
        </div>
        <textarea name="comment" class="recommends-comment" placeholder="코멘트를 남겨주세요!"></textarea>
        <div class="buttons">
            <button type="button" class="btn btn-cancel" onclick="confirmBack()">뒤로가기</button>
            <button type="submit" class="btn btn-submit">추천</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/recommends.js') }}"></script>
@endpush
