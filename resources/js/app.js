import './bootstrap';

window.openAppleMusic = function (event, appUrl, webUrl) {
    event.preventDefault();

    if (!appUrl) {
        window.open(webUrl, '_blank');
        return;
    }

    const fallback = setTimeout(() => window.open(webUrl, '_blank'), 1500);
    window.addEventListener('blur', function onBlur() {
        clearTimeout(fallback);
        window.removeEventListener('blur', onBlur);
    });
    window.location.href = appUrl;
};
