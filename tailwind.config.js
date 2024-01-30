module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      // Extend the default Tailwind theme here
      colors: {
        'primary': '#3490dc',
        'secondary': '#ffed4a',
        'danger': '#e3342f',
      },
      // Add custom fonts, spacing, etc.
    },
  },
  plugins: [
    // Add any plugins here
  ],
  // Optionally, enable JIT mode for better performance
  mode: 'jit',
}
