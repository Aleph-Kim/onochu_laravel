document.addEventListener('DOMContentLoaded', function () {
    const musicSlider = document.querySelector('.music-slider');
    const flkty = new Flickity(musicSlider, {
        autoPlay: true,
        imagesLoaded: true,
        prevNextButtons: false,
        pageDots: false,
        wrapAround: false,
    });

    // 드래그 중 페이지 스크롤과 충돌하지 않도록 터치 스크롤 차단
    flkty.on('dragStart', function () {
        document.ontouchmove = function (e) {
            e.preventDefault();
        };
    });

    flkty.on('dragEnd', function () {
        document.ontouchmove = function (e) {
            return true;
        };
    });

    // 슬라이드 5개 초과일 때만 무한 스크롤 활성화
    if (flkty.cells.length > 5) {
        flkty.options.wrapAround = true;
        flkty.updateDraggable();
    }
});
