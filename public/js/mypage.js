let genreChart;
let songs = [];
let currentSort = 'latest';
let searchQuery = '';
let chartColors = ['#FF6347', '#FFD39B', '#FFEC8B', '#98FB98', '#87CEEB', '#DDA0DD', '#FFC0CB'];

function setProfileAlbum(recommendId) {
    fetch(`/mypage/setProfileAlbum`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            recommend_id: recommendId
        })
    })
    .then(response => response.json())
    .then(data => {
        editProfile(data.album_flo_id, data.album_img_url);
        alert("프로필 앨범이 변경되었습니다.");
    })
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
        date: new Date(song.querySelector('.recommend-date').innerText.trim()),
        title: song.querySelector('.song-title').innerText.toLowerCase().trim()
    }));
}

/**
 * 앨범을 필터링 / 정렬하여 렌더링
 */
function renderSongs() {
    const songList = document.querySelector('.songs-grid');
    const filteredSongs = songs
        .filter(song =>
            (searchQuery === '' || song.title.includes(searchQuery) || separateKoreanCharacters(song.title).includes(separateKoreanCharacters(searchQuery)))
        )
        .sort((a, b) =>
            currentSort === 'latest' ? b.date - a.date : a.date - b.date
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
        latestLabel.classList.toggle('active', !sortToggle.checked);
        oldestLabel.classList.toggle('active', sortToggle.checked);
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