@props(['url', 'preferred' => null])

@php
    use App\Enums\MusicApp;

    $baseClass = 'inline-block py-[10px] px-[24px] rounded-full cursor-pointer transition-all text-white bg-primary hover:bg-primary-light font-semibold shadow-sm hover:shadow-md';
@endphp

@if($preferred === MusicApp::AppleMusic)
    <a href="#" onclick="openAppleMusicLazy(event, {{ Js::from($url['apple_music_keyword']) }})" target="_blank"
       class="{{ $baseClass }}">내 뮤직앱에서 재생하기</a>
@elseif(in_array($preferred, [MusicApp::Youtube, MusicApp::Flo, MusicApp::Spotify], true))
    <a href="{{ $url[$preferred->value] }}" target="_blank" class="{{ $baseClass }}">내 뮤직앱에서 재생하기</a>
@else
    <a href="{{ route('mypage.music-app', ['redirect' => request()->getRequestUri()]) }}"
       class="{{ $baseClass }}">내 뮤직앱에서 재생하기</a>
@endif
