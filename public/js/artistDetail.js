let albums = [];
let currentType = 'all';
let currentSort = 'latest';
let searchQuery = '';

/**
 * HTML에서 앨범 데이터 파싱
 */
function initAlbums() {
    const albumElements = document.querySelectorAll('.album');
    if (!albumElements.length) return;

    albums = Array.from(albumElements).map(album => ({
        element: album,
        type: album.querySelector('.album-type').innerText.trim(),
        date: new Date(album.querySelector('.album-date').innerText.trim()),
        title: album.querySelector('.album-title').innerText.toLowerCase().trim()
    }));
}

/**
 * 앨범을 필터링 / 정렬하여 렌더링
 */
function renderAlbums() {
    const albumList = document.querySelector('.albums');
    const filteredAlbums = albums
        .filter(album =>
            (currentType === 'all' || currentType.includes(album.type)) &&
            (searchQuery === '' || album.title.includes(searchQuery) || separateKoreanCharacters(album.title).includes(separateKoreanCharacters(searchQuery)))
        )
        .sort((a, b) =>
            currentSort === 'latest' ? b.date - a.date : a.date - b.date
        );

    albumList.innerHTML = filteredAlbums.length === 0
        ? '<div class="no-results">검색된 앨범이 없습니다.</div>'
        : '';

    filteredAlbums.forEach(album => albumList.appendChild(album.element));
}

/**
 * 이벤트 리스너 설정
 */
function bindEvents() {
    const selector = document.querySelector('.custom-selector');
    const selectedOption = selector.querySelector('.selected-option');
    const optionsContainer = selector.querySelector('.options');
    const options = optionsContainer.querySelectorAll('.option');
    const sortToggle = document.getElementById('sortToggle');
    const latestLabel = document.querySelector('.toggle-label.latest');
    const oldestLabel = document.querySelector('.toggle-label.oldest');
    const searchInput = document.getElementById('albumSearch');

    // 셀렉터 토글
    selectedOption.addEventListener('click', () =>
        optionsContainer.classList.toggle('show')
    );

    // 옵션 선택
    options.forEach(option =>
        option.addEventListener('click', () => {
            currentType = option.dataset.type;
            selectedOption.textContent = option.textContent;
            optionsContainer.classList.remove('show');
            renderAlbums();
        })
    );

    // 셀렉터 외부 클릭 시 닫기
    document.addEventListener('click', e => {
        if (!selector.contains(e.target)) optionsContainer.classList.remove('show');
    });

    // 정렬 토글
    sortToggle.addEventListener('change', () => {
        currentSort = sortToggle.checked ? 'oldest' : 'latest';
        latestLabel.classList.toggle('active', !sortToggle.checked);
        oldestLabel.classList.toggle('active', sortToggle.checked);
        renderAlbums();
    });

    // 검색 입력
    searchInput.addEventListener('input', e => {
        searchQuery = e.target.value.toLowerCase().trim();
        renderAlbums();
    });
}

/**
 * 페이지 로드 시 초기화
 */
function initialize() {
    initAlbums();
    bindEvents();
    renderAlbums();
}

// 페이지 로드 시 실행
document.addEventListener('DOMContentLoaded', initialize);