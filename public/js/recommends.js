document.querySelector('.recommends-form').addEventListener('submit', function (event) {
    event.preventDefault();

    const form = event.target;
    const submitBtn = document.querySelector('.btn-submit');

    if (form.dataset.alreadyRecommended === 'true' && !confirm('이미 추천한 노래입니다. 다시 추천하시겠습니까?')) {
        return;
    }

    showLoader();
    submitBtn.disabled = true; // 버튼 비활성화
    submitBtn.textContent = '처리 중';

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
    })
        .then(function (response) {
            window.location.href = response.url;
        })
        .catch(function () {
            hideLoader();
            submitBtn.disabled = false;
            submitBtn.textContent = '추천';
            alert('추천 등록에 실패했습니다. 다시 시도해주세요.');
        });
});

window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        hideLoader();

        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.disabled = false;
        submitBtn.textContent = '추천';
    }
});
