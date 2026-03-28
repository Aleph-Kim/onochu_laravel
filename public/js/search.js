document.addEventListener("DOMContentLoaded", () => {
    const songBoxes = document.querySelectorAll('.song-box');

    // .song-box 클릭 이벤트
    songBoxes.forEach(songBox => {
        songBox.addEventListener('click', event => {
            songBoxes.forEach(box => box.classList.remove('visible')); // 모두 숨기기
            songBox.classList.add('visible'); // 클릭한 것만 보이기
            event.stopPropagation(); // 전파 방지
        });
    });

    // 다른 곳 클릭 시 모두 숨기기
    document.addEventListener('click', event => {
        if (!event.target.closest('.song-box')) {
            songBoxes.forEach(box => box.classList.remove('visible'));
        }
    });
});