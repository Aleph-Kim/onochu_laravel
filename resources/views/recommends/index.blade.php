@extends('layouts.app')

@section('content')
    <div class="p-5 max-w-[500px] mx-auto">
        <form class="recommends-form flex flex-col items-center" action="{{ route('recommends.store') }}" method="post"
              data-already-recommended="{{ $previousRecommends->isNotEmpty() ? 'true' : 'false' }}">
            @csrf
            <input type="hidden" name="id" value="{{ $songInfo['song']['flo_id'] }}">
            @if($previousRecommends->isNotEmpty())
                <div class="w-full rounded-xl bg-surface p-4 mb-5">
                    <p class="flex items-center gap-2 text-label text-headline mb-3">
                        <svg viewBox="0 0 24 24" class="w-5 h-5 fill-star shrink-0" aria-hidden="true">
                            <path d="M12,2 L1,21 H23 L12,2 Z M11,9 H13 V14 H11 Z M11,16 H13 V18 H11 Z"></path>
                        </svg>
                        이미 이 노래를 {{ $previousRecommends->count() }}번 추천했어요
                    </p>
                    <div
                        class="flex flex-col gap-3 {{ $previousRecommends->count() > 2 ? 'max-h-[220px] overflow-y-auto pr-1' : '' }}">
                        @foreach($previousRecommends as $previous)
                            <div
                                class="flex items-start justify-between gap-3 {{ !$loop->last ? 'pb-3 border-b-[0.5px] border-separator' : '' }}">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if($previous->score)
                                            <span
                                                class="text-star text-callout tracking-tight">{{ str_repeat('★', $previous->score) }}{{ str_repeat('☆', 5 - $previous->score) }}</span>
                                        @endif
                                        <span
                                            class="text-label-secondary text-footnote tabular-nums">{{ $previous->created_at->format('Y.m.d') }}</span>
                                    </div>
                                    @if($previous->comment)
                                        <p class="text-label text-subheadline truncate">"{{ $previous->comment }}
                                            "</p>
                                    @endif
                                </div>
                                <a href="{{ route('recommends.show', $previous) }}"
                                   class="shrink-0 text-subheadline text-primary transition-opacity hover:opacity-70">자세히 보기
                                    &rsaquo;</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="flex w-full items-center gap-3 mb-5">
                <img src="{{ $songInfo['artists'][0]['img_url'] }}?/dims/resize/200x200/quality/90"
                     class="w-10 h-10 rounded-full object-cover cursor-pointer transition-opacity hover:opacity-80"
                     onclick="window.location.href = '{{ route('artist.detail', ['id' => $songInfo['artists'][0]['flo_id']]) }}'">
                <span>
                @foreach($songInfo['artists'] as $artist)
                        <span
                            class="text-subheadline text-label font-medium cursor-pointer transition-colors hover:text-primary artist-name"
                            onclick="window.location.href = '{{ route('artist.detail', ['id' => $artist['flo_id']]) }}'">{{ $artist['name'] }}</span>
                    @endforeach
            </span>
            </div>
            <div class="w-full aspect-square mb-4 overflow-hidden rounded-2xl shadow-artwork">
                <img src="{{ $songInfo['album']['img_url'] }}?/dims/resize/350x350/quality/90"
                     class="w-full h-full object-cover">
            </div>
            <div class="w-full text-left mb-4">
                <h2 class="text-title-2 mb-1 text-label">{{ $songInfo['song']['title'] }}</h2>
                <p class="text-footnote text-label-secondary flex items-center gap-2">
                    {{ $songInfo['album']['release_date'] ?? '발매일 미상' }}
                    <span class="inline-block w-px h-2.5 mx-1 my-0 bg-label-tertiary"></span>
                    {{ $songInfo['song']['genre'] }}
                    <span class="inline-block w-px h-2.5 mx-1 my-0 bg-label-tertiary"></span>
                    {{ $songInfo['song']['play_time'] }}
                </p>
            </div>
            <div class="[direction:rtl] text-[40px] my-4 mx-0">
                <input type="radio" id="star5" name="score" value="5" class="peer hidden">
                <label for="star5"
                       class="peer text-label-tertiary cursor-pointer hover:text-star peer-checked:text-star peer-hover:text-star">★</label>
                <input type="radio" id="star4" name="score" value="4" class="peer hidden">
                <label for="star4"
                       class="peer text-label-tertiary cursor-pointer hover:text-star peer-checked:text-star peer-hover:text-star">★</label>
                <input type="radio" id="star3" name="score" value="3" class="peer hidden">
                <label for="star3"
                       class="peer text-label-tertiary cursor-pointer hover:text-star peer-checked:text-star peer-hover:text-star">★</label>
                <input type="radio" id="star2" name="score" value="2" class="peer hidden">
                <label for="star2"
                       class="peer text-label-tertiary cursor-pointer hover:text-star peer-checked:text-star peer-hover:text-star">★</label>
                <input type="radio" id="star1" name="score" value="1" class="peer hidden">
                <label for="star1"
                       class="peer text-label-tertiary cursor-pointer hover:text-star peer-checked:text-star peer-hover:text-star">★</label>
            </div>
            <textarea name="comment" placeholder="코멘트를 남겨주세요!"
                      class="w-full h-[120px] p-3 rounded-xl resize-none text-body text-label mb-4 border-0 bg-fill placeholder:text-label-secondary focus:outline-none focus:shadow-[0_0_0_3px_rgb(var(--tint)/0.35)]"></textarea>
            <div class="w-full flex justify-end gap-2">
                <button type="button"
                        class="min-h-[44px] px-5 rounded-full text-headline cursor-pointer bg-fill text-primary transition-opacity hover:opacity-80 active:opacity-60"
                        onclick="confirmBack()">뒤로가기
                </button>
                <button type="submit"
                        class="btn-submit min-h-[44px] px-6 rounded-full text-headline cursor-pointer bg-primary text-white transition-opacity hover:opacity-90 active:opacity-70">
                    추천
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/recommends.js') }}"></script>
@endpush
