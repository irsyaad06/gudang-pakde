import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                brand: { 50: '#F5F3FF', 100: '#EDE9FE', 600: '#6D5CE7', 700: '#5B4BC4', 800: '#4637A5' },
                photo: '#F7F7F8',
                sold: '#FFFBEB',
                ink: { 900: '#202024', 600: '#62626B', 500: '#777780' },
                surface: '#FFFFFF',
                canvas: '#F3F3F5',
                border: '#E8E8EC',
                danger: '#DC2626',
                success: '#15803D',
                'success-soft': '#F0FDF4',
                warning: '#A16207',
                focus: '#6D5CE7',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
