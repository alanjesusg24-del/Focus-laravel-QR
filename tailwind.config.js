/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/View/Components/**/*.php",
        "./app/Livewire/**/*.php",
    ],
    theme: {
        extend: {
            colors: {
                // Colores institucionales CETAM
                'institutional-blue': '#1d4976',
                'institutional-orange': '#de5629',
                'institutional-gray': '#7b96ab',
            },
            fontFamily: {
                sans: ['system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
