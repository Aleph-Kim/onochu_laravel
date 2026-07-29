// 웹 푸시 서비스 워커 (루트 스코프 /sw.js)
self.addEventListener('push', function (event) {
    if (!event.data) {
        return;
    }

    const payload = event.data.json();

    event.waitUntil(
        self.registration.showNotification(payload.title, {
            body: payload.body,
            icon: payload.icon,
            badge: payload.badge,
            data: payload.data,
        })
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = (event.notification.data && event.notification.data.url) || '/';
    event.waitUntil(clients.openWindow(url));
});
