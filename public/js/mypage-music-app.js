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
                        alert(response.message);
                        return;
                    }

                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                        return;
                    }

                    document.querySelectorAll('.music-app-option').forEach(el => {
                        const selected = el.dataset.app === app;
                        el.classList.toggle('border-primary', selected);
                        el.classList.toggle('bg-[#f5f5fc]', selected);
                        el.classList.toggle('border-[#ebebf0]', !selected);
                        el.classList.toggle('hover:bg-[#f8f8fc]', !selected);

                        const radio = el.querySelector('.music-app-radio');
                        radio.classList.toggle('border-primary', selected);
                        radio.classList.toggle('border-[#d8d8e4]', !selected);
                        radio.querySelector('.music-app-radio-dot').classList.toggle('hidden', !selected);
                    });
                })
                .catch(() => alert('저장 중 오류가 발생했습니다.'));
        });
    });
}

document.addEventListener('DOMContentLoaded', bindMusicAppOptions);
