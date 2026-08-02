@extends('layouts.app')

@section('content')
    <div class="max-w-[1200px] mx-auto px-6 py-20 flex flex-col items-center text-center">
        <svg viewBox="0 0 24 24" class="w-20 h-20 fill-[#d8d8e8] mb-6" aria-hidden="true">
            <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
        </svg>
        <h2 class="text-2xl font-bold text-[#111] mb-2">뮤직앱으로 이동 중이에요</h2>
        <p class="text-[#8b8b9a] mb-6">잠시 후 자동으로 연결돼요. 연결되지 않으면 아래 버튼을 눌러주세요.</p>
        <button type="button" onclick="window.history.back()"
                class="py-2 px-5 rounded-full text-sm font-medium cursor-pointer bg-[#f0f0f8] text-[#555] hover:bg-[#e5e5ef] transition">
            돌아가기
        </button>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var appUrl = @json($appUrl);
            var webUrl = @json($webUrl);

            if (!appUrl) {
                window.location.replace(webUrl);
                return;
            }

            var fallback = setTimeout(function () {
                window.location.replace(webUrl);
            }, 1500);

            window.addEventListener('blur', function onBlur() {
                clearTimeout(fallback);
                window.removeEventListener('blur', onBlur);
            });

            window.location.href = appUrl;
        })();
    </script>
@endpush
