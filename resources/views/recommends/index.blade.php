@extends('layouts.app')

@section('content')
<div class="p-5 max-w-[500px] mx-auto">
    <form class="recommends-form flex flex-col items-center" action="/recommends/post" method="post">
        @csrf
        <div class="flex w-full items-center gap-3 mb-5">
            <img src="{{ $songInfo['artists'][0]['img_url'] }}?/dims/resize/200x200/quality/90"
                 class="w-10 h-10 rounded-full object-cover cursor-pointer transition hover:opacity-80 shadow-sm"
                 onclick="window.location.href = '/artist/detail?id={{ $songInfo['artists'][0]['flo_id'] }}'">
            <span>
                @foreach($songInfo['artists'] as $artist)
                    <span class="text-sm text-[#333] font-medium cursor-pointer transition hover:text-primary artist-name"
                          onclick="window.location.href = '/artist/detail?id={{ $artist['flo_id'] }}'">{{ $artist['name'] }}</span>
                @endforeach
            </span>
        </div>
        <div class="w-full aspect-square mb-4 overflow-hidden rounded-2xl">
            <img src="{{ $songInfo['album']['img_url'] }}?/dims/resize/350x350/quality/90" class="w-full h-full object-cover">
        </div>
        <div class="w-full text-left mb-4">
            <h2 class="text-[20px] font-bold mb-2 text-[#111] tracking-tight">{{ $songInfo['song']['title'] }}</h2>
            <p class="text-[13px] text-[#8b8b9a] flex items-center gap-2">
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
        <textarea name="comment" placeholder="코멘트를 남겨주세요!"
                  class="w-full h-[120px] p-4 rounded-2xl resize-none text-sm mb-4"></textarea>
        <div class="w-full flex justify-end gap-2">
            <button type="button" class="py-2 px-5 rounded-full text-sm font-medium cursor-pointer bg-[#f0f0f8] text-[#555] hover:bg-[#e5e5ef] transition"
                    onclick="confirmBack()">뒤로가기</button>
            <button type="submit" class="btn-submit py-2 px-6 rounded-full text-sm font-semibold cursor-pointer bg-primary text-white hover:bg-primary-light shadow-sm hover:shadow-md transition">추천</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/recommends.js') }}"></script>
@endpush
