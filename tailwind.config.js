/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            screens: {
                'xl': '1350px',
            },
            colors: {
                primary: '#5b5bd6',
                'primary-light': '#7c7ce8',
                'primary-dark': '#4747b8',
                youtube: '#FF0000',
                flo: '#8B00FF',
                genie: '#0F7EFF',
                spotify: '#1DB954',
                kakao: {
                    DEFAULT: '#fae100',
                    text: '#371d1e',
                },
                bg: '#f8f8fc',
            },
            fontFamily: {
                sans: ['Pretendard Variable', 'Pretendard', '-apple-system', 'BlinkMacSystemFont', 'system-ui', 'sans-serif'],
                eczar: ['Eczar', 'serif'],
            },
        },
    },
    plugins: [],
}
