@extends('layouts.app')

@section('content')
    @php
        $newAlbumSectionTitle = session('user') ? session('user.nickname') . '님이 추천한 아티스트의 신규 앨범' : '추천 많은 아티스트의 신규 앨범';
        $artistSectionTitle = session('user') ? session('user.nickname') . '님이 추천한 아티스트' : '추천 많은 아티스트';
    @endphp

    <div class="overflow-hidden">
        @if($recommends->isNotEmpty())
            <div class="music-slider py-[60px] overflow-visible focus:outline-none">
                @foreach($recommends as $recommend)
                    <a class="group [&.is-selected]:z-10" href="{{ route('recommends.show', $recommend) }}">
                        <div
                            class="p-[15px] transition-transform duration-300 group-[&.is-selected]:scale-[1.3] max-[480px]:group-[&.is-selected]:scale-110">
                            <div
                                class="w-[300px] overflow-hidden shadow-[0_8px_24px_rgba(0,0,40,0.12)] rounded-2xl transition-all duration-[400ms] bg-white">
                                <div
                                    class="group/body block h-[300px] w-[300px] bg-center bg-cover overflow-hidden transition-all duration-[400ms] relative cursor-pointer">
                                <span
                                    class="h-[100px] w-[100px] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 opacity-0 transition-opacity duration-200 group-[&.is-selected]:group-hover/body:opacity-100">
                                    <img src="{{ $recommend->song->artists->first()?->img_url }}?size=350x350"
                                         loading="lazy" class="rounded-full w-full h-full object-cover">
                                </span>
                                    <div
                                        class="h-full w-full bg-cover absolute top-0 transition-all duration-200 group-[&.is-selected]:group-hover/body:brightness-50"
                                        style="background-image: url({{ $recommend->song->album->img_url }}?size=500x500);"></div>
                                </div>
                                <div class="pt-3.5 px-5 pb-4 border-0 bg-white relative flex">
                                    <div>
                                        <span
                                            class="block leading-[1.3] font-bold text-sm text-primary">{{ $recommend->song->title }}</span>
                                        <span
                                            class="block leading-[1.3] text-xs text-[#8b8b9a]">{{ $recommend->song->artists->pluck('name')->implode(' & ') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div
                class="flex gap-4 p-2.5 overflow-hidden relative before:content-[attr(data-text)] before:whitespace-pre-wrap before:flex before:items-center before:justify-center before:w-full before:h-full before:bg-black/25 before:text-white before:text-xl before:absolute before:top-0 before:left-0 before:z-10 before:text-center"
                data-text="다른 유저들의 추천 노래를 이 곳에서 보여드릴게요!">
                <div class="flex animate-[scroll_10s_linear_infinite] gap-4 mb-2.5">
                    @for($i = 0; $i < 10; $i++)
                        <div class="w-[330px] p-[15px] shrink-0">
                            <div class="rounded-2xl overflow-hidden shadow-[0_8px_24px_rgba(0,0,40,0.12)] bg-white">
                                <div
                                    class="w-full h-[300px] animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                <div class="py-[14px] px-[20px] bg-white">
                                    <div
                                        class="h-4 mb-2 rounded-lg animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                    <div
                                        class="h-[0.8rem] rounded-lg animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>

    <div class="md:w-[70%] md:max-w-[1200px] md:mx-auto md:px-[15px]">
        <div class="mt-[30px]">
            <h2 class="text-2xl font-bold mb-6 tracking-tight pl-[15px] md:pl-0">{{ $newAlbumSectionTitle }}</h2>
            @if(!empty($newAlbums))
                <div class="flex gap-4 py-4 overflow-x-auto scrollbar-hide pl-[15px] md:pl-0">
                    @foreach($newAlbums as $newAlbum)
                        @php $artistName = implode(' & ', array_column($newAlbum['artists'], 'name')); @endphp
                        <a class="w-[200px] flex-none rounded-2xl shadow-sm border border-[#ebebf0] bg-white overflow-hidden hover:shadow-md transition-shadow"
                           href="{{ route('album.detail', ['id' => $newAlbum['flo_id'], 'new_album' => 'true']) }}">
                            <img src="{{ $newAlbum['img_url'] }}?size=350x350" loading="lazy" class="w-full"/>
                            <div class="w-[200px] p-3">
                                <h3 class="font-semibold truncate text-[#111]">{{ $newAlbum['title'] }}</h3>
                                <p class="truncate text-sm text-[#8b8b9a]">{{ $artistName }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div
                    class="flex gap-4 p-2.5 overflow-hidden relative pl-[15px] md:pl-0 before:content-[attr(data-text)] before:whitespace-pre-wrap before:flex before:items-center before:justify-center before:w-full before:h-full before:bg-black/25 before:text-white before:text-xl before:absolute before:top-0 before:left-0 before:z-10 before:text-center"
                    data-text="새로 나온 앨범이 있다면 이 곳에서 알려드릴게요!">
                    <div class="flex animate-[scroll_10s_linear_infinite] gap-4 mb-2.5">
                        @for($i = 0; $i < 10; $i++)
                            <div class="rounded-2xl shadow-sm border border-[#ebebf0] bg-white overflow-hidden">
                                <div
                                    class="w-48 h-48 rounded-lg animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                <div class="p-2">
                                    <div
                                        class="h-4 mb-2 rounded-lg animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                    <div
                                        class="h-[0.8rem] rounded-lg animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endif
        </div>

        <div class="my-[40px]">
            <h2 class="text-2xl font-bold mb-6 tracking-tight pl-[15px] md:pl-0">{{ $artistSectionTitle }}</h2>
            @if($artists->isNotEmpty())
                <div class="flex gap-4 py-4 overflow-x-auto scrollbar-hide pl-[15px] md:pl-0">
                    @foreach($artists as $artist)
                        <a class="flex-none basis-48" href="{{ route('artist.detail', ['id' => $artist->flo_id]) }}">
                            <img src="{{ $artist->img_url }}?size=350x350" loading="lazy"
                                 class="aspect-square rounded-full w-full h-auto object-cover shadow-[0_4px_16px_rgba(0,0,20,0.12)]"/>
                            <div class="p-2 text-center">
                                <p class="font-semibold line-clamp-2 text-[#111]">{{ $artist->name }}</p>
                                <div class="flex items-center justify-center gap-1 mt-1">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-[#5b5bd6]">
                                        <g class="style-scope yt-icon">
                                            <path
                                                d="M18.77,11h-4.23l1.52-4.94C16.38,5.03,15.54,4,14.38,4c-0.58,0-1.14,0.24-1.52,0.65L7,11H3v10h4h1h9.43 c1.06,0,1.98-0.67,2.19-1.61l1.34-6C21.23,12.15,20.18,11,18.77,11z M7,20H4v-8h3V20z M19.98,13.17l-1.34,6 C18.54,19.65,18.03,20,17.43,20H8v-8.61l5.6-6.06C13.79,5.12,14.08,5,14.38,5c0.26,0,0.5,0.11,0.63,0.3 c0.07,0.1,0.15,0.26,0.09,0.47l-1.52,4.94L13.18,12h1.35h4.23c0.41,0,0.8,0.17,1.03,0.46C19.92,12.61,20.05,12.86,19.98,13.17z"
                                                class="style-scope yt-icon"></path>
                                        </g>
                                    </svg>
                                    <span class="text-sm text-[#8b8b9a]">{{ $artist->recommend_cnt }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div
                    class="flex gap-4 p-2.5 overflow-hidden relative pl-[15px] md:pl-0 before:content-[attr(data-text)] before:whitespace-pre-wrap before:flex before:items-center before:justify-center before:w-full before:h-full before:bg-black/25 before:text-white before:text-xl before:absolute before:top-0 before:left-0 before:z-10 before:text-center"
                    data-text="아직 추천 아티스트가 없네요!&#10;나만의 노래를 추천해주세요!">
                    <div class="flex animate-[scroll_10s_linear_infinite] gap-4 mb-2.5">
                        @for($i = 0; $i < 10; $i++)
                            <div>
                                <div
                                    class="w-48 h-48 rounded-full mx-auto my-0 animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                <div class="p-2">
                                    <div
                                        class="h-4 w-[70%] mx-auto mt-0 mb-2 rounded-lg animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                    <div
                                        class="h-[0.8rem] w-[40%] mx-auto my-0 rounded-lg animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-[#f0f0f5] from-[25%] via-[#e8e8f0] via-[50%] to-[#f0f0f5] to-[75%] bg-[length:200%_100%]"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/lib/flickity.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
@endpush
