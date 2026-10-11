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

let toastTimer;

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.remove('opacity-0', 'translate-y-2', 'pointer-events-none');

    clearTimeout(toastTimer);
    toastTimer = setTimeout(hideToast, 3000);
}

function hideToast() {
    clearTimeout(toastTimer);
    document.getElementById('toast').classList.add('opacity-0', 'translate-y-2', 'pointer-events-none');
}

function showDialog(message, { confirmText = '확인', cancelable = false } = {}) {
    const dialog = document.getElementById('dialog');
    document.getElementById('dialogMessage').textContent = message;
    document.getElementById('dialogConfirm').textContent = confirmText;
    document.getElementById('dialogCancel').hidden = !cancelable;
    dialog.returnValue = '';
    dialog.showModal();

    return new Promise(resolve => {
        dialog.addEventListener('close', () => resolve(dialog.returnValue === 'confirm'), { once: true });
    });
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
