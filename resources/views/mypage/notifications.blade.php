@extends('layouts.app')

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
                <h1 class="text-title-1 text-label">알림 설정</h1>
            </div>
        </div>

        @if($artistList->isNotEmpty())
            <div class="max-w-[1200px] mx-auto px-6 pb-4 w-full flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-headline text-label mb-1">신곡 알림</h2>
                    <p class="text-label-secondary text-subheadline mb-4">추천한 아티스트의 신곡 알림을 개별적으로 켜고 끌 수 있어요.</p>
                </div>

                <label class="relative inline-block w-[51px] h-[31px] flex-none">
                    <input type="checkbox" id="pushToggle" class="peer opacity-0 w-0 h-0" onchange="togglePush()">
                    <span
                        class="absolute cursor-pointer inset-0 bg-track transition-colors duration-200 rounded-full peer-checked:bg-primary before:content-[''] before:absolute before:h-[27px] before:w-[27px] before:left-[2px] before:top-[2px] before:bg-white before:transition-transform before:duration-200 before:rounded-full before:shadow-knob peer-checked:before:translate-x-5"></span>
                </label>
            </div>

        @endif

        @if($artistList->isEmpty())
            <div class="max-w-[1200px] mx-auto px-6 py-20 flex flex-col items-center text-center">
                <svg viewBox="0 0 24 24" class="w-16 h-16 fill-label-tertiary mb-5" aria-hidden="true">
                    <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
                </svg>
                <h2 class="text-title-2 text-label mb-2">아직 추천한 아티스트가 없어요</h2>
                <p class="text-label-secondary">노래를 추천하면 해당 아티스트의 알림을 설정할 수 있어요.</p>
            </div>
        @else
            <div class="max-w-[1200px] mx-auto px-6 pb-12 w-full">
                <div class="mb-4">
                    <input type="text" id="artistSearch" placeholder="아티스트 검색"
                           class="w-full h-9 px-3 bg-fill border-0 rounded-[10px] text-callout text-label transition-shadow focus:shadow-[0_0_0_3px_rgb(var(--tint)/0.35)] focus:outline-none placeholder:text-label-secondary">
                </div>
                <div class="artists-list flex flex-col gap-2">
                    @foreach($artistList as $artist)
                        <div class="artist-row flex items-center p-3 rounded-xl bg-surface cursor-pointer transition-colors"
                             onclick="window.location.href = '{{ route('artist.detail', ['id' => $artist->flo_id]) }}'">
                            <img src="{{ $artist->img_url }}?/dims/resize/200x200/quality/90" alt="아티스트"
                                 class="w-12 h-12 rounded-full object-cover mr-3.5">
                            <div class="flex-grow min-w-0">
                                <h3 class="artist-row-name text-headline text-label">{{ $artist->name }}</h3>
                                <a href="{{ route('mypage.artist-recommends', $artist) }}"
                                   onclick="event.stopPropagation()"
                                   class="text-label-secondary text-subheadline hover:text-primary transition-colors">추천한
                                    노래 {{ $artist->count }}개</a>
                            </div>
                            <label class="relative inline-block w-[51px] h-[31px] flex-none" onclick="event.stopPropagation()">
                                <input type="checkbox" class="artist-notification-toggle peer opacity-0 w-0 h-0"
                                       data-artist-id="{{ $artist->id }}"
                                       data-muted="{{ in_array($artist->id, $mutedArtistIds) ? '1' : '0' }}"
                                    {{ in_array($artist->id, $mutedArtistIds) ? '' : 'checked' }}>
                                <span
                                    class="absolute cursor-pointer inset-0 bg-track transition-colors duration-200 rounded-full peer-checked:bg-primary peer-disabled:opacity-40 peer-disabled:cursor-not-allowed before:content-[''] before:absolute before:h-[27px] before:w-[27px] before:left-[2px] before:top-[2px] before:bg-white before:transition-transform before:duration-200 before:rounded-full before:shadow-knob peer-checked:before:translate-x-5"></span>
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
