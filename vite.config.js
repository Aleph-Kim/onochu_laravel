import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
