import { fetchApi, showToast } from './util.js';

// 자주 쓰는 뮤직앱 저장
function bindMusicAppOptions() {
    const container = document.querySelector('[data-redirect-url]');
    const redirectUrl = container ? container.dataset.redirectUrl : null;

    document.querySelectorAll('.music-app-option').forEach(option => {
        option.addEventListener('click', () => {
            const app = option.dataset.app;

            fetchApi('/api/music-app-preference', {app}, 'POST')
                .then(response => {
                    if (!response.success) {
                        showToast(response.message);
                        return;
                    }

                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                        return;
                    }

                    document.querySelectorAll('.music-app-option').forEach(el => {
                        const selected = el.dataset.app === app;
                        el.classList.toggle('border-primary', selected);
                        el.classList.toggle('bg-primary/10', selected);
                        el.classList.toggle('border-transparent', !selected);
                        el.classList.toggle('bg-surface', !selected);

                        const radio = el.querySelector('.music-app-radio');
                        radio.classList.toggle('border-primary', selected);
                        radio.classList.toggle('border-label-tertiary', !selected);
                        radio.querySelector('.music-app-radio-dot').classList.toggle('hidden', !selected);
                    });
                    showToast('뮤직앱 설정이 완료되었습니다.');
                })
                .catch(() => showToast('저장 중 오류가 발생했습니다.'));
        });
    });
}

document.addEventListener('DOMContentLoaded', bindMusicAppOptions);
