<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8"/>
    <title>Onochu - 오늘의 노래 추천</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)"/>
    <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="vapid-public-key" content="{{ config('webpush.public_key') }}"/>
    <link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/logo.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="default"/>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link
        href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css"
        rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="break-keep bg-canvas text-label font-sans antialiased [font-feature-settings:'calt'_1]">
<header class="w-full bg-bar backdrop-blur-xl backdrop-saturate-150 border-b-[0.5px] border-separator sticky top-0 z-[999]">
    <div
        class="mx-auto px-[10px] sm:px-[20px] md:px-[30px] lg:px-[50px] xl:px-[200px] flex items-center justify-between h-[56px]">
        <a id="logo" href="{{ route('main') }}" class="flex items-center text-primary text-[28px] font-eczar">
            <img src="{{ asset('image/logo.png') }}" class="h-[34px] mr-[6px]">
            Onochu
        </a>

        <div id="searchFormWrap" class="flex items-center justify-center">
            <button id="searchFormHideBtn" class="hidden w-[30px] h-[30px] mr-[10px]" onclick="hiddenSearchForm()">
                <svg viewBox="0 0 24 24" class="fill-primary">
                    <g mirror-in-rtl="">
                        <path d="M21,11v1H5.64l6.72,6.72l-0.71,0.71L3.72,11.5l7.92-7.92l0.71,0.71L5.64,11H21z"
                              class="style-scope yt-icon"></path>
                    </g>
                </svg>
            </button>
            <form id="searchForm" class="relative w-[300px] mx-auto max-sm:w-4/5 max-sm:mx-0 max-sm:hidden"
                  action="{{ route('search') }}">
                <input type="text" name="q" placeholder="검색어를 입력하세요" value="{{ request('q') }}" required
                       class="w-full h-9 pl-[34px] pr-3 box-border bg-fill border-0 rounded-[10px] text-callout text-label transition-shadow focus:shadow-[0_0_0_3px_rgb(var(--tint)/0.35)] focus:outline-none placeholder:text-label-secondary"/>
                <button
                    class="w-[34px] py-[9px] pl-[10px] pr-[6px] absolute left-0 top-1/2 -translate-y-1/2 cursor-pointer flex items-center">
                    <svg viewBox="0 0 24 24" class="w-full fill-label-secondary">
                        <g>
                            <path
                                d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path>
                        </g>
                    </svg>
                </button>
            </form>
        </div>

        <div id="rightBtnBox" class="flex items-center">
            <button class="hidden h-[34px] w-[34px]" onclick="showSearchForm()">
                <svg viewBox="0 0 24 24" class="fill-primary">
                    <g>
                        <path
                            d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path>
                    </g>
                </svg>
            </button>

            <div class="flex items-center ml-[30px] max-sm:ml-0">
                @if(session('user'))
                    @if(request()->is('mypage'))
                        <a href="{{ route('logout') }}" type="button"
                           class="px-3 py-1.5 text-primary text-callout cursor-pointer hover:opacity-70 active:opacity-50 transition-opacity duration-150">로그아웃</a>
                    @else
                        <a href="{{ route('mypage.index') }}" type="button"
                           class="px-3 py-1.5 text-primary text-callout cursor-pointer hover:opacity-70 active:opacity-50 transition-opacity duration-150 max-md:hidden">마이페이지</a>
                    @endif

                    <div id="notificationBox" class="relative ml-[12px] max-sm:ml-0">
                        <button id="notificationBtn" type="button"
                                class="relative h-11 w-11 flex items-center justify-center rounded-full transition-colors hover:bg-fill"
                                onclick="toggleNotificationDropdown()" aria-label="알림">
                            <svg viewBox="0 0 24 24" class="w-[22px] h-[22px] fill-primary">
                                <path
                                    d="M12 2a6 6 0 0 0-6 6v3.09c0 .48-.17.94-.47 1.31L4 14.5c-.86 1.07-.14 2.67 1.22 2.67h13.56c1.36 0 2.08-1.6 1.22-2.67l-1.53-1.9c-.3-.37-.47-.83-.47-1.31V8a6 6 0 0 0-6-6zm0 20a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22z"/>
                            </svg>
                            <span id="notificationBadge"
                                  class="{{ $unreadNotificationCount > 0 ? '' : 'hidden' }} absolute top-1 right-1 min-w-[17px] h-[17px] px-[4px] rounded-full bg-danger text-white text-[11px] font-semibold leading-[17px] text-center tabular-nums">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                        </button>
                        <div id="notificationDropdown"
                             class="hidden absolute right-0 top-[48px] w-[340px] max-h-[440px] flex flex-col bg-raised rounded-[14px] border-[0.5px] border-separator shadow-popover overflow-hidden z-[1000] max-[360px]:w-[290px]">
                            <div class="flex items-center justify-between px-4 py-3 border-b-[0.5px] border-separator flex-none">
                                <span class="text-headline text-label">알림</span>
                                <div class="flex items-center gap-4">
                                    <button type="button"
                                            class="text-subheadline text-danger transition-opacity hover:opacity-70 active:opacity-50"
                                            onclick="deleteReadNotifications()">읽은 알림 삭제</button>
                                    <button id="markAllReadBtn" type="button"
                                            class="text-subheadline text-primary transition-opacity hover:opacity-70 active:opacity-50"
                                            onclick="markAllNotificationsRead()">모두 읽음</button>
                                </div>
                            </div>
                            <div class="overflow-y-auto">
                                <div id="notificationList" class="flex flex-col divide-y-[0.5px] divide-separator"></div>
                                <div id="notificationEmpty" class="hidden py-10 text-center text-label-secondary text-subheadline">
                                    모든 알림을 확인했습니다!
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" type="button"
                       class="px-3 py-1.5 text-primary text-callout cursor-pointer hover:opacity-70 active:opacity-50 transition-opacity duration-150 max-md:hidden">로그인</a>
                @endif
            </div>
        </div>
    </div>
</header>

<main
    class="w-[70%] max-w-[1200px] mx-auto my-0 px-[15px] pb-[60px] min-h-[calc(100dvh-57px)] max-md:w-full max-md:px-[10px] max-md:pt-0 max-md:pb-[calc(64px+env(safe-area-inset-bottom))] max-[480px]:px-0">
    @yield('content')
</main>

<div
    class="loader-container fixed top-0 left-0 w-screen h-screen hidden items-center justify-center bg-black/40 backdrop-blur-sm z-[1001]">
    <div
        class="loader relative w-[120px] h-[90px] mx-auto my-0 before:content-[''] before:absolute before:bottom-[30px] before:left-[50px] before:h-[30px] before:w-[30px] before:rounded-full before:bg-primary before:animate-[loading-bounce_0.5s_ease-in-out_infinite_alternate] after:content-[''] after:absolute after:right-0 after:top-0 after:h-[7px] after:w-[45px] after:rounded after:shadow-[0_5px_0_#f2f2f2,-35px_50px_0_#f2f2f2,-70px_95px_0_#f2f2f2] after:animate-[loading-step_1s_ease-in-out_infinite]"></div>
</div>

{{-- 추천곡 재생 후 재방문 시 평가 유도 모달 --}}
@if($recommendToRate)
    <div id="song-rate-modal" onclick="if (event.target === this) this.remove()"
         class="fixed top-0 left-0 w-screen h-screen z-[1000] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-raised rounded-[20px] p-5 max-w-[340px] w-full shadow-popover">
            <p class="text-headline text-label mb-4 text-center">이전에 들었던 노래, 어떠셨나요?</p>
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('song.detail', ['id' => $recommendToRate->song->flo_id]) }}">
                    <img src="{{ $recommendToRate->song->album->img_url }}?size=200x200"
                         class="w-16 h-16 rounded-lg object-cover shrink-0 shadow-artwork">
                </a>
                <div class="min-w-0">
                    <a href="{{ route('song.detail', ['id' => $recommendToRate->song->flo_id]) }}"
                       class="block text-headline text-label truncate hover:text-primary transition-colors">{{ $recommendToRate->song->title }}</a>
                    <p class="text-subheadline text-label-secondary truncate">
                        @foreach($recommendToRate->song->artists as $artist)
                            <a href="{{ route('artist.detail', ['id' => $artist->flo_id]) }}"
                               class="hover:text-primary transition-colors">{{ $artist->name }}</a>{{ !$loop->last ? ' & ' : '' }}
                        @endforeach
                    </p>
                </div>
            </div>
            <a href="{{ route('mypage.user', $recommendToRate->user) }}"
               class="flex items-center justify-end gap-2 group mb-5 pb-4 border-b-[0.5px] border-separator">
                <div
                    class="w-6 h-6 rounded-full bg-center bg-cover shrink-0 {{ !$recommendToRate->user->profileAlbum ? 'hidden' : '' }}"
                    @if($recommendToRate->user->profileAlbum) style="background-image: url('{{ $recommendToRate->user->profileAlbum->img_url }}?size=200x200');" @endif></div>
                <span class="text-footnote text-label-secondary">
                    추천인
                    <span class="text-label font-semibold group-hover:text-primary transition-colors">
                        {{ $recommendToRate->user->nickname }}
                    </span>
                </span>
            </a>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('song-rate-modal').remove()"
                        class="flex-1 min-h-[44px] rounded-full bg-fill text-primary text-headline transition-opacity hover:opacity-80 active:opacity-60">
                    닫기
                </button>
                <a href="{{ route('recommends.create', ['id' => $recommendToRate->song->flo_id]) }}"
                   class="flex-1 min-h-[44px] flex items-center justify-center rounded-full cursor-pointer text-white bg-accent text-headline transition-opacity hover:opacity-90 active:opacity-70">
                    추천하기
                </a>
            </div>
        </div>
    </div>
@endif
<div id="toast" role="status" aria-live="polite" onclick="hideToast()"
     class="fixed left-1/2 bottom-[calc(66px+env(safe-area-inset-bottom))] z-[1002] w-max max-w-[calc(100vw-32px)] px-4 py-3 -translate-x-1/2 translate-y-2 text-subheadline text-label text-center bg-raised/90 backdrop-blur-xl border-[0.5px] border-separator rounded-2xl shadow-popover cursor-pointer opacity-0 pointer-events-none transition-[opacity,transform] duration-200 md:bottom-8 motion-reduce:translate-y-0"></div>
<dialog id="dialog" onclick="if (event.target === this) this.close()"
        class="w-[calc(100%-32px)] max-w-[340px] text-label bg-raised rounded-[20px] shadow-popover backdrop:bg-black/40 backdrop:backdrop-blur-sm">
    <form method="dialog" class="p-5">
        <p id="dialogMessage" class="mb-5 text-body text-center"></p>
        <div class="flex gap-3">
            <button id="dialogCancel" value="cancel"
                    class="flex-1 min-h-[44px] text-headline text-label bg-fill rounded-full transition-opacity hover:opacity-80 active:opacity-60">
                취소
            </button>
            <button id="dialogConfirm" value="confirm"
                    class="flex-1 min-h-[44px] text-headline text-white bg-accent rounded-full transition-opacity hover:opacity-90 active:opacity-70">
                확인
            </button>
        </div>
    </form>
</dialog>
{{-- 모바일 하단 탭 바 --}}
<nav aria-label="탭 바"
     class="md:hidden fixed bottom-0 inset-x-0 z-[998] flex bg-bar backdrop-blur-xl backdrop-saturate-150 border-t-[0.5px] border-separator pb-[env(safe-area-inset-bottom)]">
    <a href="{{ route('main') }}"
       class="flex-1 h-[50px] flex flex-col items-center justify-center gap-[2px] text-[11px] font-medium {{ request()->routeIs('main') ? 'text-primary' : 'text-label-secondary' }}">
        <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current" aria-hidden="true">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
        홈
    </a>
    <button type="button" onclick="window.scrollTo(0, 0); showSearchForm()"
            class="flex-1 h-[50px] flex flex-col items-center justify-center gap-[2px] text-[11px] font-medium {{ request()->routeIs('search') ? 'text-primary' : 'text-label-secondary' }}">
        <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current" aria-hidden="true">
            <path
                d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path>
        </svg>
        검색
    </button>
    <a href="{{ session('user') ? route('mypage.index') : route('login') }}"
       class="flex-1 h-[50px] flex flex-col items-center justify-center gap-[2px] text-[11px] font-medium {{ request()->is('mypage*') ? 'text-primary' : 'text-label-secondary' }}">
        <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current" aria-hidden="true">
            <path
                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        {{ session('user') ? '마이페이지' : '로그인' }}
    </a>
</nav>
@vite('resources/js/layout.js')
@if(session('user'))
    @vite('resources/js/notifications.js')
@endif
@stack('scripts')
</body>

</html>
