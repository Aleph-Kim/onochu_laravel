{{-- validation 에러 알림 --}}
@if ($errors->any())
    <script>
        alert('{{$errors->first()}}')
    </script>
@endif
@if (session('message'))
    <script>
        alert('{{ session('message') }}');
        @php
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
        @endphp
    </script>
@endif
<script src="{{ asset('/js/admin/jquery.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="{{ asset('/js/util.js') }}"></script>
<script>
    $('.gnb-item').click(function () {
        $(this).toggleClass('active');
    });

    $('.header-toggle-btn').click(function () {
        $('#admin_header').toggleClass('hide');
    });
</script>
</body>
</html>
