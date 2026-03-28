<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <title>Onochu - 오늘의 노래 추천</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0" />
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('image/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('image/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('image/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('image/favicon/favicon-32x32.png') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/layout.css') }}">
    @stack('styles')
</head>

<body>
    <header class="header">
        <div class="header_inner">
            <a id="logo" href="/">
                <img src="{{ asset('image/logo.svg') }}">
                Onochu
            </a>

            <div id="searchFormWrap">
                <button id="searchFormHideBtn" onclick="hiddenSearchForm()">
                    <svg viewBox="0 0 24 24"><g mirror-in-rtl=""><path d="M21,11v1H5.64l6.72,6.72l-0.71,0.71L3.72,11.5l7.92-7.92l0.71,0.71L5.64,11H21z" class="style-scope yt-icon"></path></g></svg>
                </button>
                <form id="searchForm" action="/search">
                    <input type="text" name="q" placeholder="검색어를 입력하세요" value="{{ request('q') }}" required />
                    <button class="btn-search">
                        <svg viewBox="0 0 24 24"><g><path d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path></g></svg>
                    </button>
                </form>
            </div>

            <div id="rightBtnBox">
                <button class="mobile-btn-search" onclick="showSearchForm()">
                    <svg viewBox="0 0 24 24"><g><path d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path></g></svg>
                </button>
                <div class="user-menu">
                    @if(session('user'))
                        @if(request()->is('mypage'))
                            <a href="/auth/logout" type="button" class="btn">로그아웃</a>
                        @else
                            <a href="/mypage" type="button" class="btn">마이페이지</a>
                        @endif
                    @else
                        <a href="/login" type="button" class="btn">로그인</a>
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
