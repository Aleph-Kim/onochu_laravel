function getMeta(name) {
    return document.querySelector(`meta[name="${name}"]`)?.content;
}

function showLoader() {
    document.querySelector('.loader-container').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideLoader() {
    document.querySelector('.loader-container').style.display = 'none';
    document.body.style.overflow = '';
}

function fetchApi(url, data = null, method = 'POST') {
    const isFormData = data instanceof FormData;

    const headers = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getMeta('csrf-token'),
    };

    if (!isFormData) {
        headers['Content-Type'] = 'application/json';
    }

    const options = { method, headers };

    if (data !== null) {
        options.body = isFormData ? data : JSON.stringify(data);
    }

    return fetch(url, options).then(response => response.json());
}
