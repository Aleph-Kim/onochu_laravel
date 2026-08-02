// 아티스트별 신곡 알림 on/off (마이페이지 '알림 설정' 화면)

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

document.addEventListener('DOMContentLoaded', bindArtistNotificationToggles);
