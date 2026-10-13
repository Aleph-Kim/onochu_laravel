import { hideLoader, showDialog, showLoader } from './util.js';

document.querySelector('.recommends-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    const form = event.target;
    const submitBtn = document.querySelector('.btn-submit');

    if (form.dataset.alreadyRecommended === 'true' && !await showDialog('이미 추천한 노래입니다. 다시 추천하시겠습니까?', { confirmText: '다시 추천', cancelable: true })) {
        return;
    }

    showLoader();
    submitBtn.disabled = true;
    submitBtn.textContent = '처리 중';

    form.submit();
});

window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        hideLoader();

        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.disabled = false;
        submitBtn.textContent = '추천';
    }
});
