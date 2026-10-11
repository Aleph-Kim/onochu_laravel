@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center text-large-title text-label mt-10 mb-2 mx-[15px] max-[480px]:text-title-1">500 Error</h1>
        <p class="max-w-[490px] mx-auto mb-[30px] px-[15px] text-body text-label-secondary text-center max-[480px]:text-callout">에러가 발생했습니다!</p>
        <section class="text-center text-[180px] font-extrabold my-[20px] mx-[15px] max-[480px]:text-[5.3em]">
            <span
                class="inline-block leading-[0.7] relative text-primary [perspective:1000px] [perspective-origin:500%_50%]">
                <span
                    class="inline-block relative [transform-origin:50%_100%_0px] animate-[easyoutelastic_8s_infinite]">5</span>
            </span>
            <span class="inline-block leading-[0.7] relative text-primary">0</span>
            <span
                class="inline-block leading-[0.7] relative text-primary [perspective:none] [perspective-origin:50%_50%]">
                <span
                    class="inline-block relative [transform-origin:100%_100%_0px] animate-[rotatedrop_8s_infinite]">0</span>
            </span>
        </section>
        <div class="text-center">
            <a href="{{ route('main') }}"
               class="inline-flex items-center min-h-[44px] px-6 mt-[50px] rounded-full bg-primary text-white text-headline no-underline transition-opacity hover:opacity-90 active:opacity-70">Home</a>
        </div>
    </div>
@endsection
