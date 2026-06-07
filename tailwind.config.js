module.exports = {
    content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    ],
    theme: {
        extend: {
      colors: {
        'primary': '#3498db', // مثال: اللون الأساسي (الأزرق)
        'secondary': '#2ecc71', // مثال: لون التمييز أو النجاح (الأخضر)
        'background-dark': '#1f2937', // مثال: خلفية داكنة
        },
      fontFamily: {
                sans: ["Cairo", "Figtree", ...defaultTheme.fontFamily.sans],
            },
      },
    },
  plugins: [],
}

