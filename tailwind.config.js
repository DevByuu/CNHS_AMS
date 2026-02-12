/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class', // <-- enable dark mode via class
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
