document.addEventListener("DOMContentLoaded", () => {
    const songBoxes = document.querySelectorAll('.song-box');

    const showIconBox = box => {
        const iconBox = box.querySelector('.icon-box');
        iconBox.classList.remove('hidden');
        iconBox.classList.add('flex');
    };
    const hideIconBox = box => {
        const iconBox = box.querySelector('.icon-box');
        iconBox.classList.remove('flex');
        iconBox.classList.add('hidden');
    };

    // .song-box 클릭 이벤트
    songBoxes.forEach(songBox => {
        songBox.addEventListener('click', event => {
            songBoxes.forEach(hideIconBox); // 모두 숨기기
            showIconBox(songBox); // 클릭한 것만 보이기
            event.stopPropagation(); // 전파 방지
        });
    });

    // 다른 곳 클릭 시 모두 숨기기
    document.addEventListener('click', event => {
        if (!event.target.closest('.song-box')) {
            songBoxes.forEach(hideIconBox);
        }
    });
});