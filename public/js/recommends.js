document.querySelector('.recommends-form').addEventListener('submit', function (event) {
    const loader = document.querySelector('.loader-container');
    loader.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    const submitBtn = document.querySelector('.btn-submit');
    submitBtn.disabled = true; // 버튼 비활성화
    submitBtn.textContent = '처리 중';
});