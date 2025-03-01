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
                // Primary brand colors
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',  // Main brand color
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
                // Secondary colors for accents
                secondary: {
                    50: '#f4f6f7',   // Lightest
                    100: '#e2e7e9',
                    200: '#c5cfd3',
                    300: '#a7b5bb',
                    400: '#718791',
                    500: '#102731',  // Your specified color
                    600: '#0e232c',
                    700: '#0b1d24',
                    800: '#09171d',
                    900: '#071318',  // Darkest
                },
                // Neutral colors for text and backgrounds
                neutral: {
                    50: '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    300: '#d4d4d8',
                    400: '#a1a1aa',
                    500: '#71717a',
                    600: '#52525b',
                    700: '#3f3f46',
                    800: '#27272a',
                    900: '#18181b',
                },
                // Danger colors for destructive actions
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',  // Main danger color
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                // Success, warning states
                success: '#22c55e',
                warning: '#f59e0b',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            spacing: {
                '128': '32rem',
                '144': '36rem',
            },
            borderRadius: {
                '4xl': '2rem',
            },
            fontSize: {
                'xxs': '0.625rem',
            },
            boxShadow: {
                'inner-lg': 'inset 0 2px 4px 0 rgb(0 0 0 / 0.15)',
            },
        },
    },

    plugins: [
        forms,
        // Add custom utilities if needed
        function({ addUtilities }) {
            const newUtilities = {
                '.text-shadow': {
                    'text-shadow': '0 2px 4px rgb(0 0 0 / 0.1)',
                },
                '.text-shadow-lg': {
                    'text-shadow': '0 4px 8px rgb(0 0 0 / 0.15)',
                },
            }
            addUtilities(newUtilities)
        }
    ],
};
