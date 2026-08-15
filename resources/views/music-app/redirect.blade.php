@extends('layouts.app')

@section('content')
    <div class="max-w-[1200px] mx-auto px-6 py-20 flex flex-col items-center text-center">
        <svg viewBox="0 0 24 24" class="w-20 h-20 fill-[#d8d8e8] mb-6" aria-hidden="true">
            <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
        </svg>
        <h2 class="text-2xl font-bold text-[#111] mb-2 text-balance">뮤직앱으로 이동 중이에요</h2>
        <div class="text-[#8b8b9a] mb-8 max-w-[320px] text-balance">
            <p>잠시 후 자동으로 연결돼요.</p>
            <p>연결되지 않으면 아래 버튼을 눌러주세요.</p>
        </div>
        <div class="flex flex-wrap justify-center gap-2">
            <button type="button" onclick="window.close()"
                    class="py-2 px-5 rounded-full text-sm font-medium cursor-pointer bg-[#f0f0f8] text-[#555] hover:bg-[#e5e5ef] transition">
                창 닫기
            </button>
            <a id="open-app-btn" href="{{ $appUrl }}"
               class="py-2 px-6 rounded-full text-sm font-semibold cursor-pointer bg-primary text-white hover:bg-primary-light shadow-sm hover:shadow-md transition">
                지금 앱 열기
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var appUrl = @json($appUrl);
            var webUrl = @json($webUrl);
            var openBtn = document.getElementById('open-app-btn');

            // 앱이 실제로 열리면 blur 이벤트가 발생해 폴백을 취소, 앱이 없으면 1.5초 후 웹으로 대체 이동
            function openApp() {
                var fallback = setTimeout(function () {
                    window.location.replace(webUrl);
                }, 1500);

                window.addEventListener('blur', function onBlur() {
                    clearTimeout(fallback);
                    window.removeEventListener('blur', onBlur);
                });

                window.location.href = appUrl;
            }

            openBtn.addEventListener('click', function (e) {
                e.preventDefault();
                openApp();
            });

            openApp();
        })();
    </script>
@endpush
