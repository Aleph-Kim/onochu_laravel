<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>관리자페이지</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/common.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico' )}}">
</head>
<body>
<div id="wrap">
    <main id="main" class="login_page">
        <div class="login_wrap">
            <div class="LOGO_wrap">
                <img src="{{ asset('images/admin/logo.png') }}" alt="">
            </div>

            <form action="{{ route('admin.login') }}" method="post" id="form">
                @csrf
                <div class="login_input_wrap">
                    <input type="text" name="login_id" id="id" placeholder="ID">
                    <input type="password" name="password" id="pwd" placeholder="PASSWORD">
                    @error('login_id')
                    <span class="error">{{ $message }}</span>
                    @enderror
                    @error('password')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button class="login_btn" id="submit">
                    {{ __('로그인') }}
                </button>
            </form>
            <div class="coworkerweb_logo_Wrap">
                <img src="{{ asset('/images/admin/coworkerweb_logo.svg') }}" alt="">
            </div>
        </div>
    </main>
</div>
</body>
</html>
