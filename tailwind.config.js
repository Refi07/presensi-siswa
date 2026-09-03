import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#3b82f6',
                    500: '#1e40af',
                    600: '#1e3a8a',
                    700: '#14295e',
                    800: '#0f2557',
                    900: '#0b1f3a',
                },
                accent: {
                    50: '#fcebeb',
                    100: '#f7c1c1',
                    200: '#f09595',
                    300: '#e86c6c',
                    400: '#dc2626',
                    500: '#b91c1c',
                    600: '#991b1b',
                    700: '#7f1d1d',
                    800: '#650f0f',
                    900: '#450a0a',
                },
            },
        },
    },

    plugins: [forms],
};