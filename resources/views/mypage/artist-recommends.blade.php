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
                <h1 class="text-2xl font-bold tracking-tight text-[#111]">추천한 <span
                        class="text-primary">{{ $artist->name }}</span> 노래</h1>
            </div>
        </div>

        @if($songList->isEmpty())
            <div class="max-w-[1200px] mx-auto px-6 py-20 flex flex-col items-center text-center">
                <svg viewBox="0 0 24 24" class="w-20 h-20 fill-[#d8d8e8] mb-6" aria-hidden="true">
                    <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
                </svg>
                <h2 class="text-2xl font-bold text-[#111] mb-2">추천한 노래가 없어요</h2>
            </div>
        @else
            <div class="max-w-[1200px] mx-auto px-6 pb-12 w-full">
                <div
                    class="songs-grid grid gap-3 [grid-template-columns:repeat(auto-fit,minmax(min(420px,100%),1fr))]">
                    @foreach($songList as $recommend)
                        <div class="flex items-center p-4 rounded-2xl border border-[#ebebf0] transition-all hover:shadow-md hover:border-[#d8d8e8] bg-white cursor-pointer"
                             onclick="window.location.href='{{ route('recommends.show', $recommend) }}'">
                            <img src="{{ $recommend->song->album->img_url }}?/dims/resize/200x200/quality/90"
                                 alt="앨범커버" class="w-20 h-20 rounded-xl object-cover mr-4 flex-none" loading="lazy">
                            <div class="flex-grow min-w-0">
                                <h3 class="font-semibold text-lg text-[#111] line-clamp-2 break-keep">{{ $recommend->song->title }}</h3>
                                <p class="text-[#8b8b9a] text-sm truncate">{{ $recommend->song->artists->pluck('name')->implode(' & ') }}</p>
                                <p class="text-[#b0b0c0] text-xs mt-1">{{ $recommend->created_at->format('Y.m.d') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
