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

    songBoxes.forEach(songBox => {
        songBox.addEventListener('click', event => {
            songBoxes.forEach(hideIconBox);
            showIconBox(songBox);
            // 아래 document 클릭 리스너로 전파돼 방금 연 아이콘이 바로 닫히지 않도록 방지
            event.stopPropagation();
        });
    });

    document.addEventListener('click', event => {
        if (!event.target.closest('.song-box')) {
            songBoxes.forEach(hideIconBox);
        }
    });
});