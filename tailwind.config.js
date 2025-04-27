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
    },
  },
  plugins: [],
}
