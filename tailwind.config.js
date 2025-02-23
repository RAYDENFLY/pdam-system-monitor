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
        },
    },

    plugins: [
        function ({ addComponents }) {
          addComponents({
            '@media print': {
              '@page': {
                size: '9.5in 11in', // Ukuran kertas PRS
                margin: '0.5in',    // Tambahkan margin biar rapi
              },
            },
          });
        },
      ],
};
