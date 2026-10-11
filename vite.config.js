import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/layout.js',
                'resources/js/notifications.js',
                'resources/js/main.js',
                'resources/js/search.js',
                'resources/js/songDetail.js',
                'resources/js/artistDetail.js',
                'resources/js/recommends.js',
                'resources/js/mypage.js',
                'resources/js/mypage-music-app.js',
                'resources/js/mypage-notifications.js',
                'resources/js/push.js',
            ],
            refresh: true,
        }),
    ],
    // 모바일 테스트 시 사용
    // server: {
    //     host: '0.0.0.0',
    //     hmr: {
    //         현재 네트워크 내 ip
    //         host: '192.168.219.52',
    //     },
    // },
});
