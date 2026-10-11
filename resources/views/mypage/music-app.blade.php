@extends('layouts.app')

@php
    use App\Enums\MusicApp;
@endphp

@section('content')
    <div class="flex flex-col bg-canvas">
        <div class="max-w-[1200px] mx-auto px-6 pt-8 pb-4 w-full">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('mypage.index') }}"
                   class="w-11 h-11 -ml-2.5 flex items-center justify-center rounded-full transition-colors hover:bg-fill"
                   aria-label="마이페이지로 돌아가기">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 fill-primary">
                        <path d="M21,11v1H5.64l6.72,6.72l-0.71,0.71L3.72,11.5l7.92-7.92l0.71,0.71L5.64,11H21z"/>
                    </svg>
                </a>
                <h1 class="text-title-1 text-label">뮤직앱 설정</h1>
            </div>
            <p class="text-label-secondary text-subheadline mb-4">자주 쓰는 뮤직앱을 저장하면 추천곡·곡 상세 화면에서 바로 그 앱으로 열 수 있어요.</p>
        </div>

        <div class="max-w-[1200px] mx-auto px-6 pb-12 w-full" @if($redirectUrl) data-redirect-url="{{ $redirectUrl }}" @endif>
            <div class="flex flex-col gap-2">
                @foreach(MusicApp::cases() as $app)
                    @php($selected = $preferredMusicApp === $app)
                    <button type="button" data-app="{{ $app->value }}"
                            class="music-app-option flex items-center p-3 rounded-xl border-2 cursor-pointer transition-colors {{ $selected ? 'border-primary bg-primary/10' : 'border-transparent bg-surface' }}">
                        <span class="w-10 h-10 rounded-full overflow-hidden flex-none mr-4">
                            <img src="{{ asset($app->logo()) }}" alt="{{ $app->label() }}"
                                 class="w-full h-full object-cover">
                        </span>
                        <span class="text-headline text-label flex-grow text-left">{{ $app->label() }}</span>
                        <span
                            class="music-app-radio w-6 h-6 rounded-full border-2 flex items-center justify-center flex-none {{ $selected ? 'border-primary' : 'border-label-tertiary' }}">
                            <span
                                class="music-app-radio-dot w-2.5 h-2.5 rounded-full bg-primary {{ $selected ? '' : 'hidden' }}"></span>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/mypage-music-app.js')
@endpush
