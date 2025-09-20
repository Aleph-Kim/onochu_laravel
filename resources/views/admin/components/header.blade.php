<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>관리자페이지</title>
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('/XEIcon/xeicon.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/admin/common.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/admin/output.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/admin/style.css') }}">

    {{--에디터--}}
    <link rel="stylesheet" href="{{asset('ckeditor5/style.css')}}">
    <link rel="stylesheet" href="{{asset('ckeditor5/ckeditor5.css')}}">
</head>

<body>
<div id="wrap">
    <div class="admin-container">
        <header id="admin_header">
            <div class="header-wrap">
                <img src="{{ asset('/images/admin/logo_w.png') }}" alt="" class="header-logo"
                     onclick="window.location.href = '{{ route('admin.main') }}'">

                <div class="menu-wrap row-group">
                    <div class="gnb">
                        <div class="gnb-item">
                            <a href="{{ route('admin.main') }}" class="item-default">
                                대시보드
                            </a>
                        </div>
                        <div class="gnb-item">
                            <a href="{{ route('admin.popup.index') }}" class="item-default">
                                팝업 관리
                            </a>
                        </div>
                        @can('is-superadmin')
                            <div class="gnb-item">
                                <a href="{{ route('admin.user.index') }}" class="item-default">
                                    관리자 관리
                                </a>
                            </div>
                        @endcan
                    </div>

                    <div class="header-btm">
                        <p class="copy-txt">
                            Copyright 2025 <br>
                            All rights reserved
                        </p>
                        <a href="{{ route('admin.logout') }}" class="logout-btn">
                            <i class="xi-log-out"></i>
                            로그아웃
                        </a>
                    </div>
                </div>

                <div class="header-toggle-btn">
                    <i class="xi-angle-left"></i>
                </div>
            </div>
        </header>
