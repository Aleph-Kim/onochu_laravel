function pushSupported() {
    return 'serviceWorker' in navigator && 'PushManager' in window;
}

function unsupportedReason() {
    const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent)
        || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1); // iPadOS
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;

    if (!window.isSecureContext) {
        return 'HTTPS(보안 연결)에서만 알림을 사용할 수 있어요.';
    }
    if (isIOS && !isStandalone) {
        return '아이폰은 Safari 공유 메뉴 → "홈 화면에 추가"로 앱을 설치한 뒤, 홈 화면 아이콘으로 열어야 알림을 받을 수 있어요.';
    }
    return '이 브라우저는 웹 푸시 알림을 지원하지 않습니다.';
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
}

function sendSubscription(url, subscription) {
    const json = subscription.toJSON();
    return fetchApi(url, {
        endpoint: subscription.endpoint,
        keys: json.keys,
    }, 'POST');
}

async function getSubscription() {
    const registration = await navigator.serviceWorker.getRegistration('/sw.js');
    return registration ? registration.pushManager.getSubscription() : null;
}

async function subscribePush() {
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        alert('알림 권한이 거부되었습니다. 브라우저 설정에서 허용해 주세요.');
        return false;
    }

    await navigator.serviceWorker.register('/sw.js');
    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(getMeta('vapid-public-key')),
    });

    await sendSubscription('/api/push/subscribe', subscription);
    return true;
}

async function unsubscribePush() {
    const subscription = await getSubscription();
    if (!subscription) {
        return true;
    }

    await sendSubscription('/api/push/unsubscribe', subscription);
    await subscription.unsubscribe();
    return true;
}

function setButtonState(subscribed) {
    const toggle = document.getElementById('pushToggle');
    if (!toggle) {
        return;
    }
    toggle.checked = subscribed;
}

// 다른 스크립트가 동명 함수로 재정의해 실제 동작을 채워 넣는 빈 훅
function notifyPushToggleChanged(enabled) {
}

async function togglePush() {
    const toggle = document.getElementById('pushToggle');
    const wantSubscribe = toggle.checked;

    if (!pushSupported()) {
        toggle.checked = !wantSubscribe;
        alert(unsupportedReason());
        return;
    }

    toggle.disabled = true;
    notifyPushToggleChanged(wantSubscribe);

    try {
        const ok = wantSubscribe ? await subscribePush() : await unsubscribePush();
        if (!ok) {
            toggle.checked = !wantSubscribe;
            notifyPushToggleChanged(toggle.checked);
        }
    } catch (e) {
        console.error(e);
        alert('알림 설정 중 오류가 발생했습니다.');
        toggle.checked = !wantSubscribe;
        notifyPushToggleChanged(toggle.checked);
    } finally {
        toggle.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    if (!pushSupported()) {
        return;
    }
    // 페이지 로드 시 새 sw.js 버전이 있는지 체크
    const registration = await navigator.serviceWorker.register('/sw.js');
    const subscription = await registration.pushManager.getSubscription();
    setButtonState(!!subscription);
    notifyPushToggleChanged(!!subscription);
});
