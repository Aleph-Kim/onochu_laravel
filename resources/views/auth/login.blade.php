@extends('layouts.app')

@section('content')
    <div class="min-h-[calc(100dvh-57px)] flex flex-col items-center justify-center gap-6 px-6 max-md:min-h-[calc(100dvh-57px-64px)]">
        <div class="text-center mb-2">
            <p class="text-large-title text-label mb-1">Onochu</p>
            <p class="text-label-secondary text-callout">오늘의 노래를 추천해보세요</p>
        </div>
        <a href="{{ route('login') }}"
           class="bg-[#FEE500] min-h-[50px] px-8 rounded-xl flex items-center gap-2 cursor-pointer transition-opacity hover:opacity-90 active:opacity-75 text-headline text-[#371d1e]">
            카카오로 로그인
        </a>
    </div>
@endsection
