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
                primary: '#554DB4',
                'primary-light': '#716cab',
                youtube: '#FF0000',
                flo: '#8B00FF',
                genie: '#0F7EFF',
                spotify: '#1DB954',
                kakao: {
                    DEFAULT: '#fae100',
                    text: '#371d1e',
                },
                bg: '#f5f5f5',
            },
            fontFamily: {
                eczar: ['Eczar', 'serif'],
            },
        },
    },
    plugins: [],
}
