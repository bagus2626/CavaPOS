import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import lineClamp from '@tailwindcss/line-clamp'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.blade.php',
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: 0, transform: 'translateY(60px)' },
                    '100%': { opacity: 1, transform: 'translateY(0)' },
                },
                'fade-in-down': {
                    '0%': { opacity: 0, transform: 'translateY(-60px)' },
                    '100%': { opacity: 1, transform: 'translateY(0)' },
                },
                'fade-in-left': {
                    '0%': { opacity: 0, transform: 'translateX(-60px)' },
                    '100%': { opacity: 1, transform: 'translateX(0)' },
                },
                'fade-in-right': {
                    '0%': { opacity: 0, transform: 'translateX(60px)' },
                    '100%': { opacity: 1, transform: 'translateX(0)' },
                },
            },
            animation: {
                'fade-in-up': 'fade-in-up 1s ease-out',
                'fade-in-down': 'fade-in-down 1s ease-out',
                'fade-in-left': 'fade-in-left 1s ease-out',
                'fade-in-right': 'fade-in-right 1s ease-out',
            },
            colors: {
                'choco': '#9A3F3F',
                'soft-choco': '#B86565',
            },
        },
    },

    plugins: [
        forms,
        lineClamp, // 👈 tambahkan di sini
    ],
}
