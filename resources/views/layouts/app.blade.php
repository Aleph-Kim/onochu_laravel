<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <title>Onochu - 오늘의 노래 추천</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0" />
    <link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>
    <header class="w-full bg-white border-b border-gray-200 sticky top-0 z-[999]">
        <div class="mx-auto px-[10px] sm:px-[20px] md:px-[30px] lg:px-[50px] xl:px-[200px] flex items-center justify-between h-[70px]">
            <a id="logo" href="/" class="flex items-center text-primary text-[32px] font-eczar">
                <img src="{{ asset('image/logo.png') }}" class="h-[40px] mr-[7px]">
                Onochu
            </a>

            <div id="searchFormWrap" class="flex items-center justify-center">
                <button id="searchFormHideBtn" class="hidden w-[30px] h-[30px] mr-[10px]" onclick="hiddenSearchForm()">
                    <svg viewBox="0 0 24 24" class="fill-[#afafaf]"><g mirror-in-rtl=""><path d="M21,11v1H5.64l6.72,6.72l-0.71,0.71L3.72,11.5l7.92-7.92l0.71,0.71L5.64,11H21z" class="style-scope yt-icon"></path></g></svg>
                </button>
                <form id="searchForm" class="relative w-[300px] mx-auto max-sm:w-4/5 max-sm:mx-0 max-sm:hidden" action="/search">
                    <input type="text" name="q" placeholder="검색어를 입력하세요" value="{{ request('q') }}" required class="w-full py-[10px] pl-[10px] pr-[40px] box-border border border-[#ccc] rounded text-sm" />
                    <button class="w-[38px] p-[7px] absolute right-0 top-1/2 -translate-y-1/2 cursor-pointer flex items-center">
                        <svg viewBox="0 0 24 24" class="w-full fill-[#afafaf]"><g><path d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path></g></svg>
                    </button>
                </form>
            </div>

            <div id="rightBtnBox" class="flex items-center">
                <button class="mobile-btn-search" onclick="showSearchForm()">
                    <svg viewBox="0 0 24 24" class="fill-[#afafaf]"><g><path d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path></g></svg>
                </button>
                <div class="ml-[30px] max-sm:ml-0">
                    @if(session('user'))
                        @if(request()->is('mypage'))
                            <a href="/auth/logout" type="button" class="px-3 py-1.5 text-[#333] cursor-pointer hover:text-[#a0a0a0] transition-colors duration-100">로그아웃</a>
                        @else
                            <a href="/mypage" type="button" class="px-3 py-1.5 text-[#333] cursor-pointer hover:text-[#a0a0a0] transition-colors duration-100">마이페이지</a>
                        @endif
                    @else
                        <a href="/login" type="button" class="px-3 py-1.5 text-[#333] cursor-pointer hover:text-[#a0a0a0] transition-colors duration-100">로그인</a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <div class="loader-container">
        <div class="loader"></div>
    </div>
    <script src="{{ asset('js/layout.js') }}"></script>
    @stack('scripts')
</body>

</html>
