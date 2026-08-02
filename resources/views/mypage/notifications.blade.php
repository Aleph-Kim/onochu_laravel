@extends('layouts.app')

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
                <h1 class="text-2xl font-bold tracking-tight text-[#111]">알림 설정</h1>
            </div>
        </div>

        @if($artistList->isNotEmpty())
            <div class="max-w-[1200px] mx-auto px-6 pb-4 w-full flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-[#111] mb-1">신곡 알림</h2>
                    <p class="text-[#8b8b9a] text-sm mb-4">추천한 아티스트의 신곡 알림을 개별적으로 켜고 끌 수 있어요.</p>
                </div>

                <label class="relative inline-block w-10 h-5">
                    <input type="checkbox" id="pushToggle" class="peer opacity-0 w-0 h-0" onchange="togglePush()">
                    <span
                        class="absolute cursor-pointer inset-0 bg-[#d8d8e4] transition-all duration-300 rounded-[10px] peer-checked:bg-primary before:content-[''] before:absolute before:h-4 before:w-4 before:left-0.5 before:bottom-0.5 before:bg-white before:transition-all before:duration-300 before:rounded-full before:shadow-[0_1px_3px_rgba(0,0,0,0.15)] peer-checked:before:translate-x-5"></span>
                </label>
            </div>

        @endif

        @if($artistList->isEmpty())
            <div class="max-w-[1200px] mx-auto px-6 py-20 flex flex-col items-center text-center">
                <svg viewBox="0 0 24 24" class="w-20 h-20 fill-[#d8d8e8] mb-6" aria-hidden="true">
                    <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
                </svg>
                <h2 class="text-2xl font-bold text-[#111] mb-2">아직 추천한 아티스트가 없어요</h2>
                <p class="text-[#8b8b9a]">노래를 추천하면 해당 아티스트의 알림을 설정할 수 있어요.</p>
            </div>
        @else
            <div class="max-w-[1200px] mx-auto px-6 pb-12 w-full">
                <div class="flex flex-col gap-3">
                    @foreach($artistList as $artist)
                        <div class="flex items-center p-4 rounded-2xl border border-[#ebebf0]">
                            <a href="{{ route('artist.detail', ['id' => $artist->flo_id]) }}"
                               class="flex items-center flex-grow min-w-0">
                                <img src="{{ $artist->img_url }}?/dims/resize/200x200/quality/90" alt="아티스트"
                                     class="w-14 h-14 rounded-full object-cover mr-4 shadow-sm">
                                <div class="flex-grow min-w-0">
                                    <h3 class="font-semibold text-lg text-[#111]">{{ $artist->name }}</h3>
                                    <p class="text-[#8b8b9a] text-sm">추천한 노래 {{ $artist->count }}개</p>
                                </div>
                            </a>
                            <label class="relative inline-block w-10 h-5 flex-none">
                                <input type="checkbox" class="artist-notification-toggle peer opacity-0 w-0 h-0"
                                       data-artist-id="{{ $artist->id }}"
                                       data-muted="{{ in_array($artist->id, $mutedArtistIds) ? '1' : '0' }}"
                                    {{ in_array($artist->id, $mutedArtistIds) ? '' : 'checked' }}>
                                <span
                                    class="absolute cursor-pointer inset-0 bg-[#d8d8e4] transition-all duration-300 rounded-[10px] peer-checked:bg-primary peer-disabled:opacity-40 peer-disabled:cursor-not-allowed before:content-[''] before:absolute before:h-4 before:w-4 before:left-0.5 before:bottom-0.5 before:bg-white before:transition-all before:duration-300 before:rounded-full before:shadow-[0_1px_3px_rgba(0,0,0,0.15)] peer-checked:before:translate-x-5"></span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/push.js') }}"></script>
    <script src="{{ asset('js/mypage-notifications.js') }}"></script>
@endpush
