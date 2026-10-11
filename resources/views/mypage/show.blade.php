@extends('layouts.app')

@section('content')
    <div class="flex flex-col bg-canvas">
        <a class="profile-header relative h-[340px] overflow-hidden w-full rounded-b-[2rem] before:content-[''] before:absolute before:inset-0 before:bg-gradient-to-b before:from-black/5 before:to-black/65 before:z-[1]"
           @if($user->profileAlbum) href="{{ route('album.detail', ['id' => $user->profileAlbum->flo_id]) }}" @endif>
            <div
                class="profile-background absolute inset-0 bg-center bg-cover {{ !$user->profileAlbum ? 'bg-fill-strong' : '' }}"
                @if($user->profileAlbum) style="background-image: url('{{ $user->profileAlbum->img_url }}?size=1000x1000');" @endif></div>
            <div class="max-w-[1200px] mx-auto px-6 h-full flex flex-col justify-end pb-8">
                @if($genreList)
                    <p class="text-white/75 text-callout font-medium z-[1]">{{ array_keys($genreList)[0] }} 장르를 좋아하는</p>
                @endif
                <p class="text-white text-[3.25rem] font-bold mb-1 z-[1] leading-tight tracking-tight">{{ $user->nickname }} 님</p>
                <p id="recommendCount" class="text-white/75 text-callout z-[1] tabular-nums">추천한 노래 {{ $user->recommends_count }}개</p>
            </div>
        </a>

        @if($isOwner)
            <div class="max-w-[1200px] mx-auto px-6 pt-6 w-full flex justify-end gap-2">
                <a href="{{ route('mypage.music-app') }}"
                   class="flex items-center gap-1.5 py-2 px-5 rounded-full text-subheadline font-medium cursor-pointer bg-fill text-primary transition-colors hover:bg-fill-strong">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 fill-primary">
                        <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
                    </svg>
                    뮤직앱 설정
                </a>
                <a href="{{ route('mypage.notifications') }}"
                   class="flex items-center gap-1.5 py-2 px-5 rounded-full text-subheadline font-medium cursor-pointer bg-fill text-primary transition-colors hover:bg-fill-strong">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 fill-primary">
                        <path d="M12 22c1.1 0 2-0.9 2-2h-4c0 1.1 0.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-0.83-0.67-1.5-1.5-1.5s-1.5 0.67-1.5 1.5v0.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    알림 설정
                </a>
            </div>
        @endif

        @if($user->recommends_count > 0)
            <div class="flex-grow bg-canvas">
                <div class="max-w-[1200px] mx-auto p-6 pb-0">
                    <div class="flex flex-col gap-8 xl:flex-row">
                        <div class="flex-1">
                            <h2 class="text-title-2 mb-4 text-label">좋아하는 아티스트</h2>
                            <div class="flex flex-col gap-2 max-h-[400px] overflow-y-auto scrollbar-hide">
                                @foreach($artistList as $artist)
                                    <div class="flex items-center p-3 rounded-xl bg-surface transition-opacity hover:opacity-80 active:opacity-60 cursor-pointer"
                                         onclick="window.location.href = '{{ route('artist.detail', ['id' => $artist->flo_id]) }}'">
                                        <img src="{{ $artist->img_url }}?/dims/resize/200x200/quality/90" alt="아티스트"
                                             class="w-14 h-14 rounded-full object-cover mr-3.5">
                                        <div class="flex-grow">
                                            <h3 class="text-headline text-label">{{ $artist->name }}</h3>
                                            <a href="{{ route('mypage.artist-recommends', $artist) }}"
                                               onclick="event.stopPropagation()"
                                               class="text-label-secondary text-subheadline hover:text-primary transition-colors">추천한
                                                노래 {{ $artist->count }}개</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-title-2 mb-4 text-label">좋아하는 장르</h2>
                            <div id="genreChart" class="w-full h-[400px]"></div>
                        </div>
                    </div>
                </div>
                <div class="max-w-[1200px] mx-auto p-6 pb-8">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
                        <h2 class="text-title-2 text-label">추천하는 노래</h2>
                        <div class="flex justify-evenly lg:justify-end items-center gap-5">
                            <div class="flex items-center gap-[10px]">
                                <span
                                    class="toggle-label latest text-subheadline text-primary font-semibold transition-colors duration-200">최신순</span>
                                <label class="relative inline-block w-[51px] h-[31px]">
                                    <input type="checkbox" id="sortToggle" class="peer opacity-0 w-0 h-0">
                                    <span
                                        class="absolute cursor-pointer inset-0 bg-fill-strong transition-colors duration-200 rounded-full peer-checked:bg-primary before:content-[''] before:absolute before:h-[27px] before:w-[27px] before:left-[2px] before:top-[2px] before:bg-white before:transition-transform before:duration-200 before:rounded-full before:shadow-knob peer-checked:before:translate-x-5"></span>
                                </label>
                                <span class="toggle-label oldest text-subheadline text-label-secondary transition-colors duration-200">오래된순</span>
                            </div>
                            <div>
                                <input type="text" id="songSearch" placeholder="노래 검색"
                                       class="w-full h-9 px-3 bg-fill border-0 rounded-[10px] text-callout text-label transition-shadow focus:shadow-[0_0_0_3px_rgb(var(--tint)/0.35)] focus:outline-none placeholder:text-label-secondary">
                            </div>
                        </div>
                    </div>
                    <div
                        class="songs-grid grid gap-2 [grid-template-columns:repeat(auto-fit,minmax(min(420px,100%),1fr))]">
                        @foreach($songList as $recommend)
                            <div
                                class="song-card relative flex items-center p-3 rounded-xl bg-surface cursor-pointer"
                                data-id="{{ $recommend->id }}">
                                @if($isOwner)
                                    <button type="button"
                                            class="absolute top-2 right-2 w-7 h-7 flex items-center justify-center rounded-full text-label-tertiary transition-colors hover:bg-fill hover:text-label-secondary"
                                            onclick="event.stopPropagation(); deleteRecommend('{{ $recommend->id }}')"
                                            aria-label="추천 삭제">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4 fill-current">
                                            <path
                                                d="M18.3 5.71a1 1 0 0 0-1.41 0L12 10.59 7.11 5.7A1 1 0 0 0 5.7 7.11L10.59 12l-4.9 4.89a1 1 0 1 0 1.41 1.41L12 13.41l4.89 4.9a1 1 0 0 0 1.41-1.41L13.41 12l4.9-4.89a1 1 0 0 0 0-1.4z"/>
                                        </svg>
                                    </button>
                                @endif
                                <img src="{{ $recommend->song->album->img_url }}?/dims/resize/200x200/quality/90"
                                     alt="앨범커버"
                                     class="w-[72px] h-[72px] rounded-lg object-cover mr-3.5 flex-none" loading="lazy"
                                     onclick="window.location.href='{{ route('recommends.show', $recommend) }}'">
                                <div class="flex-grow min-w-0"
                                     onclick="window.location.href='{{ route('recommends.show', $recommend) }}'">
                                    <h3 class="song-title text-headline text-label line-clamp-2 break-keep">{{ $recommend->song->title }}</h3>
                                    <p class="text-label-secondary text-subheadline truncate">
                                        @foreach($recommend->song->artists as $artist)
                                            <span class="cursor-pointer hover:text-primary transition-colors"
                                                  onclick="event.stopPropagation(); window.location.href = '{{ route('artist.detail', ['id' => $artist->flo_id]) }}'">{{ $artist->name }}</span>
                                            @if(!$loop->last)
                                                <span>&</span>
                                            @endif
                                        @endforeach
                                    </p>
                                    <p class="recommend-date text-label-secondary text-footnote mt-0.5 tabular-nums">{{ $recommend->created_at->format('Y.m.d') }}</p>
                                </div>
                                @if($isOwner)
                                    <div class="flex-none" onclick="setProfileAlbum('{{ $recommend->id }}')">
                                        <button
                                            class="py-1.5 px-3.5 rounded-full text-footnote font-semibold cursor-pointer bg-primary/10 text-primary transition-colors hover:bg-primary/20">
                                            프로필 설정
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="flex-grow bg-canvas">
                <div class="max-w-[1200px] mx-auto px-6 py-20 flex flex-col items-center text-center">
                    <svg viewBox="0 0 24 24" class="w-16 h-16 fill-label-tertiary mb-5" aria-hidden="true">
                        <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
                    </svg>
                    <h2 class="text-title-2 text-label mb-2">아직 추천한 노래가 없어요</h2>
                    @if($isOwner)
                        <p class="text-label-secondary mb-8">마음에 드는 노래를 추천하면 이 곳에 모아서 보여드릴게요.</p>
                        <a href="{{ route('main') }}"
                           class="inline-flex items-center min-h-[44px] px-6 rounded-full text-headline bg-primary text-white transition-opacity hover:opacity-90 active:opacity-70">노래
                            둘러보기</a>
                    @else
                        <p class="text-label-secondary">이 사용자가 노래를 추천하면 이 곳에 표시됩니다.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/lib/echarts.min.js') }}"></script>
    <script>
        const genreList = {{ Js::from($genreList) }};
    </script>
    <script src="{{ asset('js/mypage.js') }}"></script>
@endpush
