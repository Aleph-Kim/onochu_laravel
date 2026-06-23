@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-71px)] flex flex-col items-center justify-center gap-6">
    <div class="text-center mb-2">
        <p class="text-2xl font-bold tracking-tight text-[#111] mb-1">Onochu</p>
        <p class="text-[#8b8b9a] text-sm">오늘의 노래를 추천해보세요</p>
    </div>
    <a href="/auth/login"
       class="bg-[#FEE500] hover:bg-[#FDD835] py-[12px] px-[32px] rounded-full flex items-center gap-2 cursor-pointer transition-all font-semibold text-[#371d1e] shadow-sm hover:shadow-md">
        카카오로 로그인
    </a>
</div>
@endsection
