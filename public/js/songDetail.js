/**
 * 앨범 커버와 가사 태그 toggle 함수
 * 
 * @param {String} view 노출할 요소 (cover || lyrics)
 */
function toggleView(view) {
    const container = document.querySelector('.album-container');
    const coverBtn = document.querySelector('.cover-btn');
    const lyricsBtn = document.querySelector('.lyrics-btn');

    if (view === 'cover') {
        container.classList.remove('lyrics-active');
        lyricsBtn.classList.remove('active');
        coverBtn.classList.add('active');
    } else {
        container.classList.add('lyrics-active');
        lyricsBtn.classList.add('active');
        coverBtn.classList.remove('active');
    }
}