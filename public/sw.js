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

// iOS PWA 종료 상태에서 알림 탭 시 start_url로 강제 이동되는 문제 우회
const PENDING_REDIRECT_CACHE = 'pending-redirect';
const PENDING_REDIRECT_KEY = '/__pending_redirect__';
const PENDING_REDIRECT_TTL_MS = 30000;

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil((async () => {
        const windowClients = await clients.matchAll({ type: 'window', includeUncontrolled: true });

        if (windowClients.length > 0) {
            const client = windowClients[0];
            try {
                await client.navigate(url);
                await client.focus();
                return;
            } catch (e) {
                // navigate() 미지원 브라우저는 아래 openWindow 경로로 폴백
            }
        }

        const cache = await caches.open(PENDING_REDIRECT_CACHE);
        await cache.put(PENDING_REDIRECT_KEY, new Response(JSON.stringify({ url, ts: Date.now() })));

        await clients.openWindow(url);
    })());
});

self.addEventListener('fetch', function (event) {
    const url = new URL(event.request.url);
    const isRootNavigation = event.request.mode === 'navigate' && url.pathname === '/';

    if (!isRootNavigation) {
        return;
    }

    event.respondWith((async () => {
        const cache = await caches.open(PENDING_REDIRECT_CACHE);
        const match = await cache.match(PENDING_REDIRECT_KEY);

        if (match) {
            await cache.delete(PENDING_REDIRECT_KEY);
            const { url: pendingUrl, ts } = await match.json();

            if (Date.now() - ts < PENDING_REDIRECT_TTL_MS && pendingUrl !== url.href) {
                return Response.redirect(pendingUrl, 302);
            }
        }

        return fetch(event.request);
    })());
});
