function bindArtistNotificationToggles() {
    document.querySelectorAll('.artist-notification-toggle').forEach(toggle => {
        toggle.addEventListener('change', () => {
            const artistId = toggle.dataset.artistId;
            toggle.disabled = true;

            fetchApi(`/api/artists/${artistId}/notification-toggle`, null, 'POST')
                .then(response => {
                    if (!response.success) {
                        alert(response.message);
                        toggle.checked = !toggle.checked;
                        return;
                    }
                    toggle.checked = !response.data.muted;
                    toggle.dataset.muted = response.data.muted ? '1' : '0';
                })
                .catch(() => {
                    alert('알림 설정 중 오류가 발생했습니다.');
                    toggle.checked = !toggle.checked;
                })
                .finally(() => {
                    toggle.disabled = false;
                });
        });
    });
}

// 신곡 알림(마스터 스위치) 상태에 맞춰 아티스트별 토글을 켜고 끔 - 실제 음소거 데이터는 건드리지 않음
function notifyPushToggleChanged(enabled) {
    document.querySelectorAll('.artist-notification-toggle').forEach(toggle => {
        toggle.disabled = !enabled;
        toggle.checked = enabled && toggle.dataset.muted !== '1';
    });
}

// 초성 단위 검색 지원 (예: 'ㄱㅅ'로 '가수' 검색)
let artists = [];
let artistSearchQuery = '';

function initArtists() {
    const artistElements = document.querySelectorAll('.artist-row');
    if (!artistElements.length) return;

    artists = Array.from(artistElements).map(artist => ({
        element: artist,
        name: artist.querySelector('.artist-row-name').innerText.toLowerCase().trim()
    }));
}

function renderArtists() {
    const artistList = document.querySelector('.artists-list');
    const decomposedQuery = separateKoreanCharacters(artistSearchQuery);
    const filteredArtists = artists.filter(artist =>
        artistSearchQuery === '' || artist.name.includes(artistSearchQuery) || (decomposedQuery !== '' && separateKoreanCharacters(artist.name).includes(decomposedQuery))
    );

    artistList.innerHTML = filteredArtists.length === 0
        ? `<div class="no-results flex flex-col items-center text-center py-16">
            <svg viewBox="0 0 24 24" class="w-12 h-12 fill-[#d8d8e8] mb-4" aria-hidden="true"><g><path d="M20.87,20.17l-5.59-5.59C16.35,13.35,17,11.75,17,10c0-3.87-3.13-7-7-7s-7,3.13-7,7s3.13,7,7,7c1.75,0,3.35-0.65,4.58-1.71 l5.59,5.59L20.87,20.17z M10,16c-3.31,0-6-2.69-6-6s2.69-6,6-6s6,2.69,6,6S13.31,16,10,16z"></path></g></svg>
            <p class="text-[#8b8b9a] text-sm">검색된 아티스트가 없습니다.</p>
        </div>`
        : '';

    filteredArtists.forEach(artist => artistList.appendChild(artist.element));
}

function bindArtistSearch() {
    const searchInput = document.getElementById('artistSearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', e => {
        artistSearchQuery = e.target.value.toLowerCase().trim();
        renderArtists();
    });
}

document.addEventListener('DOMContentLoaded', bindArtistNotificationToggles);
document.addEventListener('DOMContentLoaded', () => {
    initArtists();
    bindArtistSearch();
});
