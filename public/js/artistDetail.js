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
    const decomposedQuery = separateKoreanCharacters(searchQuery);
    const filteredAlbums = albums
        .filter(album =>
            (currentType === 'all' || currentType.includes(album.type)) &&
            (searchQuery === '' || album.title.includes(searchQuery) || (decomposedQuery !== '' && separateKoreanCharacters(album.title).includes(decomposedQuery)))
        )
        .sort((a, b) =>
            currentSort === 'latest' ? b.date - a.date : a.date - b.date
        );

    albumList.innerHTML = filteredAlbums.length === 0
        ? `<div class="no-results col-span-full flex flex-col items-center text-center py-16">
            <svg viewBox="0 0 24 24" class="w-12 h-12 fill-[#d8d8e8] mb-4" aria-hidden="true"><g><path d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path></g></svg>
            <p class="text-[#8b8b9a] text-sm">검색된 앨범이 없습니다.</p>
        </div>`
        : '';

    filteredAlbums.forEach(album => albumList.appendChild(album.element));
}

/**
 * 모바일 화면에서 앨범 검색창을 펼치는 함수
 */
function showAlbumSearch() {
    document.getElementById('albumTypeFilter').style.display = 'none';
    document.getElementById('albumSortToggle').style.display = 'none';
    document.getElementById('albumSearchIconBtn').style.display = 'none';

    const searchWrap = document.getElementById('albumSearchWrap');
    searchWrap.style.display = 'flex';
    searchWrap.style.width = '100%';
    searchWrap.style.maxWidth = 'none';

    document.getElementById('albumSearchHideBtn').style.display = 'block';
    document.getElementById('albumSearch').focus();
}

/**
 * 모바일 화면에서 앨범 검색창을 접는 함수
 */
function hideAlbumSearch() {
    document.getElementById('albumTypeFilter').style.display = '';
    document.getElementById('albumSortToggle').style.display = '';
    document.getElementById('albumSearchIconBtn').style.display = '';

    const searchWrap = document.getElementById('albumSearchWrap');
    searchWrap.style.display = '';
    searchWrap.style.width = '';
    searchWrap.style.maxWidth = '';

    document.getElementById('albumSearchHideBtn').style.display = 'none';
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
        optionsContainer.classList.toggle('hidden')
    );

    // 옵션 선택
    options.forEach(option =>
        option.addEventListener('click', () => {
            currentType = option.dataset.type;
            selectedOption.textContent = option.textContent;
            optionsContainer.classList.add('hidden');
            renderAlbums();
        })
    );

    // 셀렉터 외부 클릭 시 닫기
    document.addEventListener('click', e => {
        if (!selector.contains(e.target)) optionsContainer.classList.add('hidden');
    });

    // 정렬 토글
    sortToggle.addEventListener('change', () => {
        currentSort = sortToggle.checked ? 'oldest' : 'latest';
        setToggleLabelActive(latestLabel, !sortToggle.checked);
        setToggleLabelActive(oldestLabel, sortToggle.checked);
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