/**
 * 앨범 커버와 가사 태그 toggle 함수
 * 
 * @param {String} view 노출할 요소 (cover || lyrics)
 */
function toggleView(view) {
    const overlay = document.querySelector('.lyrics-overlay');
    const coverBtn = document.querySelector('.cover-btn');
    const lyricsBtn = document.querySelector('.lyrics-btn');

    if (view === 'cover') {
        overlay.classList.remove('opacity-100', 'overflow-y-auto');
        overlay.classList.add('opacity-0');
        setToggleBtnActive(lyricsBtn, false);
        setToggleBtnActive(coverBtn, true);
    } else {
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100', 'overflow-y-auto');
        setToggleBtnActive(lyricsBtn, true);
        setToggleBtnActive(coverBtn, false);
    }
}