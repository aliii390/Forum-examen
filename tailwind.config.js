/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      fontFamily: {
        Roboto :  ["Roboto", "sans-serif"],
      },
      colors: {
        'so-blue': '#0077CC',
        'so-light-blue': '#E1ECF4',
    }
    },
  },
  plugins: [],
}
