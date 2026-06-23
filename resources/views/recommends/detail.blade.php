@extends('layouts.app')

@section('content')
<div class="p-5 max-w-[500px] mx-auto flex flex-col items-center">
    <div class="flex w-full items-center gap-3 mb-5">
        <img src="{{ $recommendInfo['artists'][0]['img_url'] }}?size=200x200"
             class="w-10 h-10 rounded-full object-cover cursor-pointer transition hover:opacity-80 shadow-sm"
             onclick="window.location.href = '/artist/detail?id={{ $recommendInfo['artists'][0]['flo_id'] }}'">
        <span>
            @foreach($recommendInfo['artists'] as $artist)
                <span class="text-sm text-[#333] font-medium cursor-pointer transition hover:text-primary artist-name"
                      onclick="window.location.href = '/artist/detail?id={{ $artist['flo_id'] }}'">{{ $artist['name'] }}</span>
            @endforeach
        </span>
    </div>
    <div class="flex justify-end w-full gap-[10px] mb-5 max-w-[600px]">
        <a href="{{ $recommendInfo['url']['youtube'] }}" target="_blank"
           class="flex items-center justify-center py-[9px] px-[14px] rounded-full border-2 cursor-pointer flex-none sm:flex-1 border-youtube text-youtube text-sm font-semibold hover:bg-youtube hover:text-white transition-colors">YouTube Music</a>
        <a href="{{ $recommendInfo['url']['flo'] }}" target="_blank"
           class="flex items-center justify-center py-[9px] px-[14px] rounded-full border-2 cursor-pointer flex-none sm:flex-1 border-flo text-flo text-sm font-semibold hover:bg-flo hover:text-white transition-colors">FLO</a>
        <a href="{{ $recommendInfo['url']['spotify'] }}" target="_blank"
           class="flex items-center justify-center py-[9px] px-[14px] rounded-full border-2 cursor-pointer flex-none sm:flex-1 border-spotify text-spotify text-sm font-semibold hover:bg-spotify hover:text-white transition-colors">Spotify</a>
    </div>
    <div class="w-full aspect-square mb-4 overflow-hidden relative rounded-2xl">
        <img src="{{ $recommendInfo['album_img_url'] }}?size=500x500"
             class="w-full h-full object-cover cursor-pointer"
             onclick="window.location.href = '/song/detail?id={{ $recommendInfo['song_flo_id'] }}'">
        <span class="share-btn">
            <span class="tooltip">공유하기</span>
            <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" fill="currentColor" viewBox="0 0 208 191.94">
                <g><polygon class="cls-1" points="76.01 89.49 87.99 89.49 87.99 89.49 82 72.47 76.01 89.49" /><path class="cls-1" d="M104,0C46.56,0,0,36.71,0,82c0,29.28,19.47,55,48.75,69.48-1.59,5.49-10.24,35.34-10.58,37.69,0,0-.21,1.76.93,2.43a3.14,3.14,0,0,0,2.48.15c3.28-.46,38-24.81,44-29A131.56,131.56,0,0,0,104,164c57.44,0,104-36.71,104-82S161.44,0,104,0ZM52.53,69.27c-.13,11.6.1,23.8-.09,35.22-.06,3.65-2.16,4.74-5,5.78a1.88,1.88,0,0,1-1,.07c-3.25-.64-5.84-1.8-5.92-5.84-.23-11.41.07-23.63-.09-35.23-2.75-.11-6.67.11-9.22,0-3.54-.23-6-2.48-5.85-5.83s1.94-5.76,5.91-5.82c9.38-.14,21-.14,30.38,0,4,.06,5.78,2.48,5.9,5.82s-2.3,5.6-5.83,5.83C59.2,69.38,55.29,69.16,52.53,69.27Zm50.4,40.45a9.24,9.24,0,0,1-3.82.83c-2.5,0-4.41-1-5-2.65l-3-7.78H72.85l-3,7.78c-.58,1.63-2.49,2.65-5,2.65a9.16,9.16,0,0,1-3.81-.83c-1.66-.76-3.25-2.86-1.43-8.52L74,63.42a9,9,0,0,1,8-5.92,9.07,9.07,0,0,1,8,5.93l14.34,37.76C106.17,106.86,104.58,109,102.93,109.72Zm30.32,0H114a5.64,5.64,0,0,1-5.75-5.5V63.5a6.13,6.13,0,0,1,12.25,0V98.75h12.75a5.51,5.51,0,1,1,0,11Zm47-4.52A6,6,0,0,1,169.49,108L155.42,89.37l-2.08,2.08v13.09a6,6,0,0,1-12,0v-41a6,6,0,0,1,12,0V76.4l16.74-16.74a4.64,4.64,0,0,1,3.33-1.34,6.08,6.08,0,0,1,5.9,5.58A4.7,4.7,0,0,1,178,67.55L164.3,81.22l14.77,19.57A6,6,0,0,1,180.22,105.23Z" /></g>
            </svg>
        </span>
    </div>
    <div class="w-full text-left mb-4">
        <a href="/song/detail?id={{ $recommendInfo['song_flo_id'] }}">
            <h2 class="text-[20px] font-bold mb-2 text-[#111] tracking-tight hover:text-primary transition-colors">{{ $recommendInfo['song_title'] }}</h2>
        </a>
        <p class="text-[13px] text-[#8b8b9a] flex items-center gap-2">
            {{ $recommendInfo['release_date'] ?? '발매일 미상' }}
            <span class="between-bar"></span>
            {{ $recommendInfo['genre'] }}
            <span class="between-bar"></span>
            {{ $recommendInfo['play_time'] }}
        </p>
    </div>
    <div class="w-full flex flex-col items-center">
        <div class="recommends-rating readonly">
            @foreach([5,4,3,2,1] as $star)
                <input type="radio" id="star{{ $star }}" name="score" value="{{ $star }}" {{ $recommendInfo['score'] == $star ? 'checked' : '' }} disabled>
                <label for="star{{ $star }}">★</label>
            @endforeach
        </div>
        <textarea name="comment" disabled
                  class="w-full h-[120px] p-4 border-0 rounded-2xl bg-[#f0f0f8] resize-none text-sm mb-4 placeholder:text-[#b0b0c0] focus:outline-none"
                  placeholder="작성된 코멘트가 없습니다.">{{ $recommendInfo['comment'] }}</textarea>
        <div class="w-full text-sm text-[#8b8b9a] text-right">추천인:
            <a href="/mypage/user?id={{ $recommendInfo['user_id'] }}" class="hover:text-primary transition-colors font-medium">{{ $recommendInfo['user_name'] }}</a>
        </div>
        <div class="w-full text-sm text-[#8b8b9a] text-right">추천일: {{ date('Y년 m월 d일', strtotime($recommendInfo['recommend_date'])) }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/lib/kakao.min.js') }}"></script>
<script>
    Kakao.init('{{ config('services.kakao.share_secret') }}');
    Kakao.Share.createCustomButton({
        container: '.share-btn',
        templateId: {{ config('services.kakao.share_template_id') }},
        templateArgs: {
            RECOMMEND_ID: '{{ $recommendInfo['id'] }}',
            ALBUM_IMG_URL: '{{ $recommendInfo['album_img_url'] }}?size=500x500',
            SONG_TITLE: '{{ $recommendInfo['song_title'] }}',
            ARTIST_NAME: '{{ $recommendInfo['artists'][0]['name'] }}',
            YOUTUBE_Q: "{{ $recommendInfo['song_title'] . ' ' . $recommendInfo['artists'][0]['name'] }}",
            FLO_ID: '{{ $recommendInfo['song_flo_id'] }}'
        },
    });
</script>
@endpush
