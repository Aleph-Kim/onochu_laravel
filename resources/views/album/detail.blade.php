@extends('layouts.app')

@section('content')
<div class="p-5 md:py-10 md:px-[30px]">
    <div class="flex items-center mb-[60px] flex-wrap max-lg:flex-col max-lg:text-center max-lg:gap-5">
        <img src="{{ $albumInfo['img_url'] }}?/dims/resize/500x500/quality/90" class="w-[250px] h-[250px] rounded-lg object-cover mr-5 max-lg:mr-0 max-sm:w-[200px] max-sm:h-[200px]">
        <div>
            <div class="text-[2.5em] font-bold max-sm:text-[2em]">{{ $albumInfo['title'] }}</div>
            <div class="mt-5 text-[#999]">
                <span>{{ $albumInfo['type'] }}</span>
                <span class="between-bar"></span>
                <span>{{ $albumInfo['genre'] }}</span>
            </div>
        </div>
    </div>
    <div class="text-2xl font-semibold mb-5">수록곡</div>
    <div class="ml-[30px] max-sm:ml-0">
        @foreach($songsInfo as $song)
            <div class="song-box flex justify-between h-[110px] w-full p-5 transition-all hover:rounded-[10px] hover:bg-[#e5e5e5] [&:not(:last-child)]:border-b [&:not(:last-child)]:border-[#e5e5e5]">
                <div class="flex flex-1 min-w-0 overflow-hidden">
                    <span class="w-[70px] mr-[10px] flex-shrink-0 max-sm:mr-0">
                        <img src="{{ $albumInfo['img_url'] }}?/dims/resize/350x350/quality/90" alt="" class="h-full rounded-[3px]">
                    </span>
                    <div class="max-w-[75%] px-[10px] flex flex-col justify-around">
                        <div class="text-primary overflow-hidden text-ellipsis whitespace-nowrap">
                            @if($song['song']['title_yn'] == 'Y')
                                <span class="text-[0.9em] font-bold text-white bg-primary border border-primary py-[2px] px-[5px] mr-[5px] inline-block">title</span>
                            @endif
                            {{ $song['song']['title'] }}
                        </div>
                        <div class="overflow-hidden text-ellipsis whitespace-nowrap text-[#999] text-sm">
                            <span>
                                @foreach($song['artists'] as $artist)
                                    <span class="artist-name">{{ $artist['name'] }}</span>
                                @endforeach
                            </span>
                            <span class="between-bar"></span>
                            <span>{{ $song['album']['title'] }}</span>
                            <span class="between-bar"></span>
                            <span>{{ $song['song']['play_time'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="icon-box hidden items-center flex-shrink-0 gap-[5px]">
                    <a href="/recommends?id={{ $song['song']['flo_id'] }}" class="flex p-[5px] fill-[#5f5f5f] cursor-pointer rounded-[45%] transition-all hover:fill-white hover:bg-[#5f5f5f]">
                        <svg viewBox="0 0 24 24" class="h-[35px]"><g><path d="M18.77,11h-4.23l1.52-4.94C16.38,5.03,15.54,4,14.38,4c-0.58,0-1.14,0.24-1.52,0.65L7,11H3v10h4h1h9.43 c1.06,0,1.98-0.67,2.19-1.61l1.34-6C21.23,12.15,20.18,11,18.77,11z M7,20H4v-8h3V20z M19.98,13.17l-1.34,6 C18.54,19.65,18.03,20,17.43,20H8v-8.61l5.6-6.06C13.79,5.12,14.08,5,14.38,5c0.26,0,0.5,0.11,0.63,0.3 c0.07,0.1,0.15,0.26,0.09,0.47l-1.52,4.94L13.18,12h1.35h4.23c0.41,0,0.8,0.17,1.03,0.46C19.92,12.61,20.05,12.86,19.98,13.17z" class="style-scope yt-icon"></path></g></svg>
                    </a>
                    <a href="/song/detail?id={{ $song['song']['flo_id'] }}" class="flex p-[5px] fill-[#5f5f5f] cursor-pointer rounded-[45%] transition-all hover:fill-white hover:bg-[#5f5f5f]">
                        <svg viewBox="0 0 24 24" class="h-[35px]"><g><path d="M16,6v2h-2v5c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2c0.37,0,0.7,0.11,1,0.28V6H16z M18,20H4V6H3v15h15V20z M21,3H6v15h15V3z M7,4h13v13H7V4z" class="style-scope yt-icon"></path></g></svg>
                    </a>
                    <a href="/artist/detail?id={{ $song['artists'][0]['flo_id'] }}" class="flex p-[5px] fill-[#5f5f5f] cursor-pointer rounded-[45%] transition-all hover:fill-white hover:bg-[#5f5f5f]">
                        <svg viewBox="0 0 24 24" class="h-[35px]"><g><path d="M22,10h-4v2v3.51C17.58,15.19,17.07,15,16.5,15c-1.38,0-2.5,1.12-2.5,2.5c0,1.38,1.12,2.5,2.5,2.5 c1.36,0,2.46-1.08,2.5-2.43V12h3V10z M3.06,19c0.38-3.11,2.61-6.1,7.94-6.1c0.62,0,1.19,0.05,1.73,0.13l0.84-0.84 c-0.58-0.13-1.19-0.23-1.85-0.26C13.58,11.59,15,9.96,15,8c0-2.21-1.79-4-4-4C8.79,4,7,5.79,7,8c0,1.96,1.42,3.59,3.28,3.93 C4.77,12.21,2,15.76,2,20h10.02L12,19H3.06z M8,8c0-1.65,1.35-3,3-3s3,1.35,3,3s-1.35,3-3,3S8,9.65,8,8z" class="style-scope yt-icon"></path></g></svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
