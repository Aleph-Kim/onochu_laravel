document.addEventListener('DOMContentLoaded', function () {
    // 슬라이드 생성
    const musicSlider = document.querySelector('.music-slider');
    const flkty = new Flickity(musicSlider, {
        autoPlay: true,
        imagesLoaded: true,
        prevNextButtons: false,
        pageDots: false,
        wrapAround: false,
    });

    // 드래그 시작 시 터치 이벤트의 기본 동작 방지
    flkty.on('dragStart', function () {
        document.ontouchmove = function (e) {
            e.preventDefault();
        };
    });

    // 드래그 종료 시 터치 이벤트의 기본 동작 복원
    flkty.on('dragEnd', function () {
        document.ontouchmove = function (e) {
            return true;
        };
    });

    // 슬라이드가 5개 미만일 경우 무한 스크롤 삭제
    if (flkty.cells.length > 5) {
        flkty.options.wrapAround = true;
        flkty.updateDraggable();
    }
});
