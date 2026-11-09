import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';

const brand = {
    50: colors.teal[50],
    100: colors.teal[100],
    200: colors.teal[200],
    300: colors.teal[300],
    400: colors.teal[500],
    500: colors.teal[600],
    600: colors.teal[700],
    700: colors.teal[800],
    800: colors.teal[900],
    900: colors.teal[950],
    950: colors.teal[950],
};

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Inter Variable"', 'Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                gray: colors.zinc,
                brand,
                indigo: brand,
            },

            // Sudut lebih lembut: kartu = lg (12px), tombol & input = md (8px)
            borderRadius: {
                md: '0.5rem',
                lg: '0.75rem',
            },

            // Bayangan tipis seperti garis tepi (hairline), bukan bayangan tebal
            boxShadow: {
                sm: '0 0 0 1px rgb(24 24 27 / 0.06), 0 1px 2px 0 rgb(24 24 27 / 0.04)',
                DEFAULT: '0 0 0 1px rgb(24 24 27 / 0.06), 0 1px 3px 0 rgb(24 24 27 / 0.06)',
                md: '0 4px 12px -2px rgb(24 24 27 / 0.08), 0 0 0 1px rgb(24 24 27 / 0.05)',
                lg: '0 12px 28px -6px rgb(24 24 27 / 0.14), 0 0 0 1px rgb(24 24 27 / 0.05)',
            },

            // `tracking-widest` bawaan Breeze terlalu renggang untuk tombol
            letterSpacing: {
                widest: '0.01em',
            },
        },
    },

    plugins: [forms],
};