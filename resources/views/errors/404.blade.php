@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center text-[2.5em] my-[30px] mx-[15px] max-[480px]:text-[2em]">404 Error</h1>
        <p class="max-w-[490px] mx-auto my-[30px] text-[1.3em] text-center max-[480px]:text-[1em]">잘못된 url로 접근하셨습니다!</p>
        <section class="text-center text-[180px] font-extrabold my-[20px] mx-[15px] max-[480px]:text-[5.3em]">
            <span class="inline-block leading-[0.7] relative text-primary [perspective:1000px] [perspective-origin:500%_50%]">
                <span class="inline-block relative [transform-origin:50%_100%_0px] animate-[easyoutelastic_8s_infinite]">4</span>
            </span>
            <span class="inline-block leading-[0.7] relative text-primary">0</span>
            <span class="inline-block leading-[0.7] relative text-primary [perspective:none] [perspective-origin:50%_50%]">
                <span class="inline-block relative [transform-origin:100%_100%_0px] animate-[rotatedrop_8s_infinite]">4</span>
            </span>
        </section>
        <div class="text-center">
            <a href="/" class="uppercase text-[13px] bg-[#bbb] py-[10px] px-[15px] text-white inline-block mr-[5px] mb-[5px] leading-[1.5] no-underline mt-[50px] tracking-[1px]">Home</a>
        </div>
    </div>
@endsection
