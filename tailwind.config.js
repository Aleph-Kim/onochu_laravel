/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './public/js/**/*.js',
    ],
    theme: {
        extend: {
            screens: {
                'xl': '1350px',
            },
            colors: {
                primary: 'rgb(var(--tint) / <alpha-value>)',
                youtube: '#FF0000',
                flo: '#8B00FF',
                genie: '#0F7EFF',
                spotify: '#1DB954',
                apple: '#000000',
                kakao: {
                    DEFAULT: '#fae100',
                    text: '#371d1e',
                },
                canvas: 'rgb(var(--canvas) / <alpha-value>)',
                surface: 'rgb(var(--surface) / <alpha-value>)',
                raised: 'rgb(var(--raised) / <alpha-value>)',
                'raised-surface': 'rgb(var(--raised-surface) / <alpha-value>)',
                segment: 'rgb(var(--segment) / <alpha-value>)',
                fill: 'var(--fill)',
                'fill-strong': 'var(--fill-strong)',
                bar: 'var(--bar)',
                label: {
                    DEFAULT: 'rgb(var(--label) / <alpha-value>)',
                    secondary: 'var(--label-secondary)',
                    tertiary: 'var(--label-tertiary)',
                },
                separator: 'var(--separator)',
                danger: 'rgb(var(--danger) / <alpha-value>)',
                star: 'rgb(var(--star) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['-apple-system', 'BlinkMacSystemFont', '"Apple SD Gothic Neo"', '"Pretendard Variable"', 'Pretendard', 'system-ui', 'sans-serif'],
                eczar: ['Eczar', 'serif'],
            },
            // Apple HIG 텍스트 스타일 (iOS 기본 크기 기준)
            fontSize: {
                'large-title': ['34px', {lineHeight: '41px', fontWeight: '700'}],
                'title-1': ['28px', {lineHeight: '34px', fontWeight: '700'}],
                'title-2': ['22px', {lineHeight: '28px', fontWeight: '700'}],
                'title-3': ['20px', {lineHeight: '25px', fontWeight: '600'}],
                headline: ['17px', {lineHeight: '22px', fontWeight: '600'}],
                body: ['17px', {lineHeight: '22px'}],
                callout: ['16px', {lineHeight: '21px'}],
                subheadline: ['15px', {lineHeight: '20px'}],
                footnote: ['13px', {lineHeight: '18px'}],
                caption: ['12px', {lineHeight: '16px'}],
                'caption-2': ['11px', {lineHeight: '13px'}],
            },
            boxShadow: {
                artwork: '0 1px 3px rgb(0 0 0 / 0.08), 0 8px 24px rgb(0 0 0 / 0.12)',
                popover: '0 2px 6px rgb(0 0 0 / 0.08), 0 12px 32px rgb(0 0 0 / 0.16)',
                knob: '0 1px 1px rgb(0 0 0 / 0.06), 0 3px 8px rgb(0 0 0 / 0.15)',
            },
        },
    },
    plugins: [],
}
