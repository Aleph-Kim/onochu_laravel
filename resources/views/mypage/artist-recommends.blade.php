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
                <h1 class="text-title-1 text-label">추천한 <span
                        class="text-primary">{{ $artist->name }}</span> 노래</h1>
            </div>
        </div>

        @if($songList->isEmpty())
            <div class="max-w-[1200px] mx-auto px-6 py-20 flex flex-col items-center text-center">
                <svg viewBox="0 0 24 24" class="w-16 h-16 fill-label-tertiary mb-5" aria-hidden="true">
                    <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
                </svg>
                <h2 class="text-title-2 text-label mb-2">추천한 노래가 없어요</h2>
            </div>
        @else
            <div class="max-w-[1200px] mx-auto px-6 pb-12 w-full">
                <div
                    class="songs-grid grid gap-2 [grid-template-columns:repeat(auto-fit,minmax(min(420px,100%),1fr))]">
                    @foreach($songList as $recommend)
                        <div class="flex items-center p-3 rounded-xl bg-surface transition-opacity hover:opacity-80 active:opacity-60 cursor-pointer"
                             onclick="window.location.href='{{ route('recommends.show', $recommend) }}'">
                            <img src="{{ $recommend->song->album->img_url }}?/dims/resize/200x200/quality/90"
                                 alt="앨범커버" class="w-[72px] h-[72px] rounded-lg object-cover mr-3.5 flex-none" loading="lazy">
                            <div class="flex-grow min-w-0">
                                <h3 class="text-headline text-label line-clamp-2 break-keep">{{ $recommend->song->title }}</h3>
                                <p class="text-label-secondary text-subheadline truncate">
                                    @foreach($recommend->song->artists as $artist)
                                        <span class="cursor-pointer hover:text-primary transition-colors"
                                              onclick="event.stopPropagation(); window.location.href = '{{ route('artist.detail', ['id' => $artist->flo_id]) }}'">{{ $artist->name }}</span>
                                        @if(!$loop->last)
                                            <span>&</span>
                                        @endif
                                    @endforeach
                                </p>
                                <p class="text-label-secondary text-footnote mt-0.5 tabular-nums">{{ $recommend->created_at->format('Y.m.d') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
