/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#9d2449',
        'primary-dark': '#7a1d37',
        'primary-light': '#b83055',
        'primary-50': '#fdf2f5',
        'primary-100': '#fce7ec',
        'primary-200': '#f9d0db'
      },
      boxShadow: {
        'button': '0 8px 25px rgba(157, 36, 73, 0.25)',
        'button-hover': '0 12px 35px rgba(157, 36, 73, 0.35)'
      }
    },
  },
  plugins: [],
} 