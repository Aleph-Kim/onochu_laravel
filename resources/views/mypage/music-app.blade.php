@extends('layouts.app')

@php
    use App\Enums\MusicApp;
@endphp

@section('content')
    <div class="flex flex-col bg-white">
        <div class="max-w-[1200px] mx-auto px-6 pt-8 pb-4 w-full">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('mypage.index') }}"
                   class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-[#f0f0f8] transition"
                   aria-label="마이페이지로 돌아가기">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 fill-[#555]">
                        <path d="M21,11v1H5.64l6.72,6.72l-0.71,0.71L3.72,11.5l7.92-7.92l0.71,0.71L5.64,11H21z"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-[#111]">뮤직앱 설정</h1>
            </div>
            <p class="text-[#8b8b9a] text-sm mb-4">자주 쓰는 뮤직앱을 저장하면 추천곡·곡 상세 화면에서 바로 그 앱으로 열 수 있어요.</p>
        </div>

        <div class="max-w-[1200px] mx-auto px-6 pb-12 w-full" @if($redirectUrl) data-redirect-url="{{ $redirectUrl }}" @endif>
            <div class="flex flex-col gap-3">
                @foreach(MusicApp::cases() as $app)
                    @php($selected = $preferredMusicApp === $app)
                    <button type="button" data-app="{{ $app->value }}"
                            class="music-app-option flex items-center p-4 rounded-2xl border cursor-pointer transition-colors {{ $selected ? 'border-primary bg-[#f5f5fc]' : 'border-[#ebebf0] hover:bg-[#f8f8fc]' }}">
                        <span class="w-10 h-10 rounded-full overflow-hidden flex-none mr-4">
                            <img src="{{ asset($app->logo()) }}" alt="{{ $app->label() }}"
                                 class="w-full h-full object-cover">
                        </span>
                        <span class="font-semibold text-[#111] flex-grow text-left">{{ $app->label() }}</span>
                        <span
                            class="music-app-radio w-6 h-6 rounded-full border-2 flex items-center justify-center flex-none {{ $selected ? 'border-primary' : 'border-[#d8d8e4]' }}">
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
    <script src="{{ asset('js/mypage-music-app.js') }}"></script>
@endpush
