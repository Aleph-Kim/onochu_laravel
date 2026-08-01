let genreChart;
let songs = [];
let currentSort = 'latest';
let searchQuery = '';
let chartColors = ['#FF6347', '#FFD39B', '#FFEC8B', '#98FB98', '#87CEEB', '#DDA0DD', '#FFC0CB'];

function setProfileAlbum(recommendId) {
    fetchApi(`/api/mypage/profile-album/${recommendId}`, null, 'POST')
        .then(response => {
            if (!response.success) {
                alert(response.message);
                return;
            }
            editProfile(response.data.album_flo_id, response.data.album_img_url);
            alert("프로필 앨범이 변경되었습니다.");
        })
        .catch(() => {
            alert("프로필 앨범 변경 중 오류가 발생했습니다.");
        })
}

function deleteRecommend(recommendId) {
    if (!confirm("이 추천을 삭제하시겠습니까?")) return;

    fetchApi(`/api/recommends/${recommendId}`, null, 'DELETE')
        .then(response => {
            if (!response.success) {
                alert(response.message);
                return;
            }
            removeSongCard(recommendId);
            if (response.data.profile_reset) {
                resetProfileBackground();
            }
        })
        .catch(() => {
            alert("추천 삭제 중 오류가 발생했습니다.");
        });
}

function resetProfileBackground() {
    const profileHeader = document.querySelector('.profile-header');
    const profileBackground = document.querySelector('.profile-background');
    profileHeader.removeAttribute('href');
    profileBackground.style.backgroundImage = '';
    profileBackground.classList.add('bg-[#d8d8e8]');
}

function removeSongCard(recommendId) {
    const id = Number(recommendId);
    const song = songs.find(s => s.id === id);
    if (!song) return;

    song.element.remove();
    songs = songs.filter(s => s.id !== id);
    renderSongs();

    const countEl = document.getElementById('recommendCount');
    if (countEl) {
        countEl.textContent = `추천한 노래 ${songs.length}개`;
    }
}

function editProfile(albumFloId, albumImgUrl) {
    const profileHeader = document.querySelector('.profile-header');
    const profileBackground = document.querySelector('.profile-background');
    profileHeader.href = `/album/detail?id=${albumFloId}`;
    profileBackground.style.backgroundImage = `url(${albumImgUrl})`;
}

/**
 * HTML에서 앨범 데이터 파싱
 */
function initSongs() {
    const songElements = document.querySelectorAll('.song-card');
    if (!songElements.length) return;

    songs = Array.from(songElements).map(song => ({
        element: song,
        id: Number(song.dataset.id),
        title: song.querySelector('.song-title').innerText.toLowerCase().trim()
    }));
}

/**
 * 앨범을 필터링 / 정렬하여 렌더링
 */
function renderSongs() {
    const songList = document.querySelector('.songs-grid');
    const decomposedQuery = separateKoreanCharacters(searchQuery);
    const filteredSongs = songs
        .filter(song =>
            (searchQuery === '' || song.title.includes(searchQuery) || (decomposedQuery !== '' && separateKoreanCharacters(song.title).includes(decomposedQuery)))
        )
        .sort((a, b) =>
            currentSort === 'latest' ? b.id - a.id : a.id - b.id
        );

    songList.innerHTML = filteredSongs.length === 0
        ? '<div class="no-results">검색된 노래가 없습니다.</div>'
        : '';

    filteredSongs.forEach(song => songList.appendChild(song.element));
}

/**
 * 이벤트 리스너 설정
 */
function bindEvents() {
    const sortToggle = document.getElementById('sortToggle');
    const latestLabel = document.querySelector('.toggle-label.latest');
    const oldestLabel = document.querySelector('.toggle-label.oldest');
    const searchInput = document.getElementById('songSearch');

    // 정렬 토글
    sortToggle.addEventListener('change', () => {
        currentSort = sortToggle.checked ? 'oldest' : 'latest';
        setToggleLabelActive(latestLabel, !sortToggle.checked);
        setToggleLabelActive(oldestLabel, sortToggle.checked);
        renderSongs();
    });

    // 검색 입력
    searchInput.addEventListener('input', e => {
        searchQuery = e.target.value.toLowerCase().trim();
        renderSongs();
    });
}

function createChartData() {
    const chartData = [];
    const genreLength = Object.keys(genreList).length;
    chartColors = chartColors.sort(() => Math.random() - 0.5);
    for (let i = 0; i < Math.min(5, genreLength); i++) {
        const genre = Object.keys(genreList)[i];
        chartData.push({
            name: genre,
            value: genreList[genre],
            itemStyle: {
                color: chartColors[i]
            }
        });
    }
    if (genreLength > 5) {
        chartData.push({
            name: '기타',
            value: genreList['기타'],
            itemStyle: {
                color: '#afafaf'
            }
        });
    }
    return chartData;
}

/**
 * 장르 차트 초기화
 */
function initChart() {
    genreChart = echarts.init(document.getElementById("genreChart"));
    const option = {
        tooltip: {
            trigger: "item"
        },
        legend: {},
        series: [{
            type: "pie",
            radius: ["40%", "70%"],
            itemStyle: {
                borderRadius: 8,
                borderColor: "#fff",
                borderWidth: 2
            },
            label: {
                show: true,
                position: "inside",
                formatter: "{b}\n{d}%",
                color: "#1a1a1a",
                fontSize: 10,
            },
            data: createChartData()
        }]
    };
    genreChart.setOption(option);
}

/**
 * 페이지 로드 시 초기화
 */
function initialize() {
    initChart();
    initSongs();
    bindEvents();
    renderSongs();
}

document.addEventListener("DOMContentLoaded", function () {
    initialize();
    window.addEventListener("resize", function () {
        genreChart.resize();
    });
});