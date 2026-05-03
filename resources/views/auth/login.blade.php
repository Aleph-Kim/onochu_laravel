@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-71px)] flex flex-col items-center justify-center gap-6">
    <a href="/auth/login"
       class="bg-[#FEE500] hover:bg-[#FDD835] py-[10px] px-[25px] rounded-lg flex items-center cursor-pointer transition-colors">
        카카오로 로그인
    </a>
</div>
@endsection
